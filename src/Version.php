<?php

namespace Cubedtear\Semver;

/**
 *  A sample class
 *
 *  Use this section to define what this class is doing, the PHPDocumentator will use this
 *  to automatically generate an API documentation using this information.
 *
 *  @author yourname
 */
class Version
{
   // Official Semver regex taken from https://semver.org/
   private static string $version_regex = "/^(?P<major>0|[1-9]\d*)\.(?P<minor>0|[1-9]\d*)\.(?P<patch>0|[1-9]\d*)(?:-(?P<prerelease>(?:0|[1-9]\d*|\d*[a-zA-Z-][0-9a-zA-Z-]*)(?:\.(?:0|[1-9]\d*|\d*[a-zA-Z-][0-9a-zA-Z-]*))*))?(?:\+(?P<buildmetadata>[0-9a-zA-Z-]+(?:\.[0-9a-zA-Z-]+)*))?$/";

   /**  @var string $major  */
   private int $major;

   /**  @var string $minor  */
   private int $minor;

   /**  @var string $patch  */
   private int $patch;

   /**  @var string $prerelease  */
   private string $prerelease;

   /**  @var string $build_metadata  */
   private string $build_metadata;

   /**
    * Constructs a version with the given parameters
    *
    * Note: No validation is done in this constructor.
    *
    * @param int $major The major version
    * @param int $minor The minor version
    * @param int $patch The patch version
    * @param string $prerelease The pre-release version. May have several components separated by a dot.
    * @param string $build_metadata The build metadata. May have several components separated by a dot.
    */
   function __construct(int $major, int $minor, int $patch, string $prerelease = "", string $build_metadata = "")
   {
      $this->major = $major;
      $this->minor = $minor;
      $this->patch = $patch;
      $this->prerelease = $prerelease;
      $this->build_metadata = $build_metadata;
   }

   public function getMajor() {
      return $this->major;
   }

   public function getMinor() {
      return $this->minor;
   }

   public function getPatch() {
      return $this->patch;
   }

   public function getPrerelease() {
      return $this->prerelease;
   }

   public function getBuildMetadata() {
      return $this->build_metadata;
   }

   public function toString() {
      $result = "$this->major.$this->minor.$this->patch";
      if (strlen($this->prerelease) > 0) {
         $result = $result . "-" . $this->prerelease;
      }
      if (strlen($this->build_metadata) > 0) {
         $result = $result . "+" . $this->build_metadata;
      }
      return $result;
   }

   /**
    * Parses a string as if it was a semantic version.
    *
    * @param string $str The version string to parse
    *
    * @return Version A positive integer if $a is newer than $b, 0 if both are equivalent, or a negative integer if $b is newer than $a.
    *
    * @throws VersionParsingException if the given string is not a valid Semver version
    */
   public static function parse(string $str)
   {
      if (preg_match(Version::$version_regex, $str, $matches)) {
         $major = $matches["major"];
         $minor = $matches["minor"];
         $patch = $matches["patch"];

         if (isset($matches["prerelease"])) {
            $prerelease = $matches["prerelease"];
         } else {
            $prerelease = "";
         }

         if (isset($matches["buildmetadata"])) {
            $buildmetadata = $matches["buildmetadata"];
         } else {
            $buildmetadata = "";
         }

         return new Version($major, $minor, $patch, $prerelease, $buildmetadata);
      } else {
         throw new VersionParsingException($str);
      }

   }

   /**
    * Compare two semantic versions to see which one is newer
    *
    * @param Version|string $a The first version to compare
    * @param Version|string $b The second version to compare
    *
    * @return int A positive integer if $a is newer than $b, 0 if both are equivalent, or a negative integer if $b is newer than $a.
    */
   public static function compare(Version|string $a, Version|string $b)
   {
      // Semver 11.1: Precedence MUST be calculated by separating the version into major, minor, patch and pre-release identifiers in
      //              that order (Build metadata does not figure into precedence).
      if (is_string($a)) {
         $a = Version::parse($a);
      }
      if (is_string($b)) {
         $b = Version::parse($b);
      }

      // Semver 11.2: Precedence is determined by the first difference when comparing each of these identifiers from left to right as
      //              follows: Major, minor, and patch versions are always compared numerically.
      if ($a->major != $b->major) {
         return $a->major - $b->major;
      }
      if ($a->minor != $b->minor) {
         return $a->minor - $b->minor;
      }
      if ($a->patch != $b->patch) {
         return $a->patch - $b->patch;
      }

      // Semver 11.3: When major, minor, and patch are equal, a pre-release version has lower precedence than a normal version
      $a_is_prerelease = strlen($a->prerelease) > 0;
      $b_is_prerelease = strlen($b->prerelease) > 0;
      if ($a_is_prerelease != $b_is_prerelease) {
         return $b_is_prerelease - $a_is_prerelease;
      }

      // Semver 11.4: Precedence for two pre-release versions with the same major, minor, and patch version MUST be determined by
      //              comparing each dot separated identifier from left to right until a difference is found as follows
      $a_prerelease_parts = explode('.', $a->prerelease);
      $b_prerelease_parts = explode('.', $b->prerelease);
      for ($index = 0; $index < min(count($a_prerelease_parts), count($b_prerelease_parts)); ++$index) {
         $a_part = $a_prerelease_parts[$index];
         $b_part = $b_prerelease_parts[$index];

         if (is_numeric($a_part) && is_numeric($b_part) && ($value = intval($a_part) - intval($b_part)) != 0) {
            return $value; // Semver 11.4.1: Identifiers consisting of only digits are compared numerically
         } else if (is_numeric($a_part) || is_numeric($b_part)) {
            // Semver 11.4.3: Numeric identifiers always have lower precedence than non-numeric identifiers
            return is_numeric($b_part) - is_numeric($a_part);
         } else if (($value = strcmp($a_part, $b_part)) != 0) {
            return $value; // Semver 11.4.2: Identifiers with letters or hyphens are compared lexically in ASCII sort order
         }
      }

      // Semver 11.4.4: A larger set of pre-release fields has a higher precedence than a smaller set, if all of the preceding
      //                identifiers are equal.
      return count($a_prerelease_parts) - count($b_prerelease_parts);
   }
}


class VersionParsingException extends \Exception
{
   // Redefine the exception so message isn't optional
   public function __construct($version_str, \Throwable $previous = null)
   {
      parent::__construct("'$version_str' is not a valid semver string", 0, $previous);
   }

   // custom string representation of object
   public function __toString()
   {
      return __CLASS__ . ": $this->message\n";
   }
}
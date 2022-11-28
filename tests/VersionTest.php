<?php

namespace Cubedtear\Semver\Tests;

use Cubedtear\Semver\Version;
use PHPUnit\Framework\TestCase;

/**
*  Corresponding Class to test YourClass class
*
*  For each class in your library, there should be a corresponding Unit-Test for it
*  Unit-Tests should be as much as possible independent from other test going on.
*
*  @author yourname
*/
class VersionTest extends TestCase
{

  /**
  * Just check if the YourClass has no syntax error
  *
  * This is just a simple check to make sure your library has no syntax error. This helps you troubleshoot
  * any typo before you even use this library in a real project.
  *
  */
  public function testBasicOrdering()
  {
    $this->assertEquals(true, $this->check_version_relation('0.0.1', '<', '0.0.2'));
    $this->assertEquals(true, $this->check_version_relation('0.0.1', '<', '0.1.0'));
    $this->assertEquals(true, $this->check_version_relation('0.0.1', '<', '0.1.1'));
    $this->assertEquals(true, $this->check_version_relation('0.0.1', '<', '1.0.0'));
    $this->assertEquals(true, $this->check_version_relation('0.0.1', '<', '1.1.0'));
    $this->assertEquals(true, $this->check_version_relation('0.0.1', '<', '1.0.1'));
    $this->assertEquals(true, $this->check_version_relation('0.0.1', '<', '1.1.1'));

    $this->assertEquals(false, $this->check_version_relation('0.0.1', '>', '0.0.2'));
    $this->assertEquals(false, $this->check_version_relation('0.0.1', '>', '0.1.0'));
    $this->assertEquals(false, $this->check_version_relation('0.0.1', '>', '0.1.1'));
    $this->assertEquals(false, $this->check_version_relation('0.0.1', '>', '1.0.0'));
    $this->assertEquals(false, $this->check_version_relation('0.0.1', '>', '1.1.0'));
    $this->assertEquals(false, $this->check_version_relation('0.0.1', '>', '1.0.1'));
    $this->assertEquals(false, $this->check_version_relation('0.0.1', '>', '1.1.1'));
  }

  public function testBasicInequalities()
  {
    $this->assertEquals(false, $this->check_version_relation('0.0.1', '==', '0.0.2'));
    $this->assertEquals(false, $this->check_version_relation('0.0.1', '==', '0.1.0'));
    $this->assertEquals(false, $this->check_version_relation('0.0.1', '==', '0.1.1'));
    $this->assertEquals(false, $this->check_version_relation('0.0.1', '==', '1.0.0'));
    $this->assertEquals(false, $this->check_version_relation('0.0.1', '==', '1.1.0'));
    $this->assertEquals(false, $this->check_version_relation('0.0.1', '==', '1.0.1'));
    $this->assertEquals(false, $this->check_version_relation('0.0.1', '==', '1.1.1'));

    $this->assertEquals(true, $this->check_version_relation('0.0.1', '!=', '0.0.2'));
    $this->assertEquals(true, $this->check_version_relation('0.0.1', '!=', '0.1.0'));
    $this->assertEquals(true, $this->check_version_relation('0.0.1', '!=', '0.1.1'));
    $this->assertEquals(true, $this->check_version_relation('0.0.1', '!=', '1.0.0'));
    $this->assertEquals(true, $this->check_version_relation('0.0.1', '!=', '1.1.0'));
    $this->assertEquals(true, $this->check_version_relation('0.0.1', '!=', '1.0.1'));
    $this->assertEquals(true, $this->check_version_relation('0.0.1', '!=', '1.1.1'));
  }

  public function testMetadataIsIgnored()
  {
    $this->assertEquals(true, $this->check_version_relation('0.0.1+build.metadata', '<', '0.0.2'));
    $this->assertEquals(true, $this->check_version_relation('0.0.1+build.metadata', '<', '0.1.0'));
    $this->assertEquals(true, $this->check_version_relation('0.0.1+build.metadata', '<', '0.1.1'));
    $this->assertEquals(true, $this->check_version_relation('0.0.1+build.metadata', '<', '1.0.0'));
    $this->assertEquals(true, $this->check_version_relation('0.0.1+build.metadata', '<', '1.1.0'));
    $this->assertEquals(true, $this->check_version_relation('0.0.1+build.metadata', '<', '1.0.1'));
    $this->assertEquals(true, $this->check_version_relation('0.0.1+build.metadata', '<', '1.1.1'));

    $this->assertEquals(true, $this->check_version_relation('0.0.1', '<', '0.0.2+build-metadata'));
    $this->assertEquals(true, $this->check_version_relation('0.0.1', '<', '0.1.0+build-metadata'));
    $this->assertEquals(true, $this->check_version_relation('0.0.1', '<', '0.1.1+build-metadata'));
    $this->assertEquals(true, $this->check_version_relation('0.0.1', '<', '1.0.0+build-metadata'));
    $this->assertEquals(true, $this->check_version_relation('0.0.1', '<', '1.1.0+build-metadata'));
    $this->assertEquals(true, $this->check_version_relation('0.0.1', '<', '1.0.1+build-metadata'));
    $this->assertEquals(true, $this->check_version_relation('0.0.1', '<', '1.1.1+build-metadata'));

    $this->assertEquals(true, $this->check_version_relation('0.0.1', '==', '0.0.1+build-metadata'));
    $this->assertEquals(true, $this->check_version_relation('0.0.1', '==', '0.0.1+build.metadata'));
    $this->assertEquals(false, $this->check_version_relation('0.0.1', '!=', '0.0.1+build-metadata'));
    $this->assertEquals(false, $this->check_version_relation('0.0.1', '!=', '0.0.1+build.metadata'));

    $this->assertEquals(true, $this->check_version_relation('0.0.1+build-metadata', '==', '0.0.1+build-metadata'));
    $this->assertEquals(true, $this->check_version_relation('0.0.1+build.metadata', '==', '0.0.1+build.metadata'));
    $this->assertEquals(false, $this->check_version_relation('0.0.1+build-metadata', '!=', '0.0.1+build-metadata'));
    $this->assertEquals(false, $this->check_version_relation('0.0.1+build.metadata', '!=', '0.0.1+build.metadata'));

  }

  public function testPrereleaseIsBeforeNonPrerelease()
  {
    $this->assertEquals(true, $this->check_version_relation('0.0.1-alpha', '<', '0.0.1'));
    $this->assertEquals(true, $this->check_version_relation('0.0.1-alpha', '<', '0.0.1-beta'));
    $this->assertEquals(true, $this->check_version_relation('0.0.1-beta', '<', '0.0.1-beta1'));
    $this->assertEquals(true, $this->check_version_relation('0.0.1-b1', '<', '0.0.1-beta'));
    $this->assertEquals(true, $this->check_version_relation('0.0.1-b', '<', '0.0.1-beta'));

    $this->assertEquals(true, $this->check_version_relation('0.0.1-alpha', '<', '0.0.1+build.metadata'));
    $this->assertEquals(true, $this->check_version_relation('0.0.1-alpha', '<', '0.0.1-beta+build.metadata'));
    $this->assertEquals(true, $this->check_version_relation('0.0.1-beta', '<', '0.0.1-beta1+build.metadata'));
    $this->assertEquals(true, $this->check_version_relation('0.0.1-b1', '<', '0.0.1-beta+build.metadata'));
    $this->assertEquals(true, $this->check_version_relation('0.0.1-b', '<', '0.0.1-beta+build.metadata'));
    $this->assertEquals(true, $this->check_version_relation('0.0.1-beta.2', '>', '0.0.1-beta'));
    $this->assertEquals(true, $this->check_version_relation('0.0.1-beta.2', '==', '0.0.1-beta.2'));
  }

  public function testMetadataDoesNotAffectPrerelease()
  {
    $this->assertEquals(true, $this->check_version_relation('0.0.1-beta', '==', '0.0.1-beta+metadata'));
  }

  private static function check_version_relation(Version|string $v1, string $op, Version|string $v2) {
    $result = Version::compare($v1, $v2);

    if ($result < 0) {
        return $op === "<" || $op === "<=" || $op === "!=";
    } else if ($result === 0) {
        return $op === "<=" || $op === "==" || $op === ">=";
    } else if ($result > 0) {
        return $op === ">" || $op === ">=" || $op === "!=";
    }
    die("What? " . $result);
  }
}

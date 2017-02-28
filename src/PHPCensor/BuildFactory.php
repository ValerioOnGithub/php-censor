<?php
/**
* PHPCI - Continuous Integration for PHP
*
* @copyright    Copyright 2014, Block 8 Limited.
* @license      https://github.com/Block8/PHPCI/blob/master/LICENSE.md
* @link         https://www.phptesting.org/
*/

namespace PHPCensor;

use b8\Store\Factory;
use PHPCensor\Model\Build;
use PHPCensor\Store\BuildStore;

/**
* PHPCI Build Factory - Takes in a generic "Build" and returns a type-specific build model.
* @author   Dan Cryer <dan@block8.co.uk>
*/
class BuildFactory
{
    /**
     * @param integer $buildId
     * 
     * @return Build
     * 
     * @throws \Exception
     */
    public static function getBuildById($buildId)
    {
        /** @var BuildStore $buildStore */
        $buildStore = Factory::getStore('Build');
        $build      = $buildStore->getById($buildId);

        if (empty($build)) {
            throw new \Exception('Build Id ' . $buildId . ' does not exist.');
        }

        return self::getBuild($build);
    }

    /**
     * @param integer $buildId
     *
     * @return Build
     *
     * @throws \Exception
     */
    public static function getBuildByIdPerProject($buildId)
    {
        /** @var BuildStore $buildStore */
        $buildStore = Factory::getStore('Build');
        $build      = $buildStore->getByIdPerProject($buildId);

        if (empty($build)) {
            throw new \Exception('Build IdPerProject ' . $buildId . ' does not exist.');
        }

        return self::getBuild($build);
    }

    /**
    * Takes a generic build and returns a type-specific build model.
    * @param Build $build The build from which to get a more specific build type.
    * @return Build
    */
    public static function getBuild(Build $build)
    {
        $project = $build->getProject();

        if (!empty($project)) {
            switch ($project->getType()) {
                case 'remote':
                    $type = 'RemoteGitBuild';
                    break;
                case 'local':
                    $type = 'LocalBuild';
                    break;
                case 'github':
                    $type = 'GithubBuild';
                    break;
                case 'bitbucket':
                    $type = 'BitbucketBuild';
                    break;
                case 'gitlab':
                    $type = 'GitlabBuild';
                    break;
                case 'hg':
                    $type = 'MercurialBuild';
                    break;
                case 'svn':
                    $type = 'SubversionBuild';
                    break;
                case 'gogs':
                    $type = 'GogsBuild';
                    break;
                default:
                    return $build;
            }

            $class = '\\PHPCensor\\Model\\Build\\' . $type;
            $build = new $class($build->getDataArray());
        }

        return $build;
    }
}

<?php
/**
 * Copyright 2015 SongPhi
 * 
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy
 * of the License at
 * 
 *   http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

// refresh static cache
$plugin = OW::getPluginManager()->getPlugin('spvideolite');
$staticDir = OW_DIR_STATIC_PLUGIN . $plugin->getModuleName() . DS;
$pluginStaticDir = OW_DIR_PLUGIN . $plugin->getModuleName() . DS . 'static' . DS;

error_reporting(E_ALL);
ini_set('display_errors', 'On');

if ( file_exists($staticDir) ) {
    @UTIL_File::removeDir($staticDir);
}

@mkdir($staticDir);
@chmod($staticDir, 0777);
@UTIL_File::copyDir($pluginStaticDir, $staticDir );

@Updater::getLanguageService()->importPrefixFromZip(dirname(dirname(dirname(__FILE__))).DS.'langs.zip', 'spvideolite');

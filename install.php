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

try {
    BOL_LanguageService::getInstance()->addPrefix('spvideolite','Super Video Lite');
} catch (Exception $e) {
    
}

@$path = OW::getPluginManager()->getPlugin('spvideolite')->getRootDir() . 'langs.zip';
@BOL_LanguageService::getInstance()->importPrefixFromZip($path, 'spvideolite');

@OW::getPluginManager()->addPluginSettingsRouteName('spvideolite', 'spvideolite.admin');

$sql = "SHOW COLUMNS FROM `".OW_DB_PREFIX."video_clip` LIKE 'plugin';";
$cols = OW::getDbo()->queryForList($sql);

if (!count($cols)) {
  $sql = "ALTER TABLE `".OW_DB_PREFIX."video_clip` ADD `plugin` VARCHAR(255) NULL DEFAULT 'video' ; ";
  OW::getDbo()->update($sql);
}

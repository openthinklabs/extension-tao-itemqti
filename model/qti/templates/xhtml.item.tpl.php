<?php
/*
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2013 (original work) Open Assessment Techonologies SA (under the project TAO-PRODUCT);
 *               
 */
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?=get_data('title')?></title>

        <?if(tao_helpers_Mode::is('production')):?>
            <link rel="stylesheet" type="text/css" href="<?=get_data('ctx_qtiDefaultRenderer_lib_www')?>css/qtiDefaultRenderer.min.css" media="screen" />
        <?else:?>
            <link rel="stylesheet" type="text/css" href="<?=get_data('ctx_qtiDefaultRenderer_lib_www')?>css/qti.css" media="screen" />
            <link rel="stylesheet" type="text/css" href="<?=get_data('ctx_qtiDefaultRenderer_lib_www')?>../../css/normalize.css" media="screen" />
            <link rel="stylesheet" type="text/css" href="<?=get_data('ctx_qtiDefaultRenderer_lib_www')?>../../css/base.css" media="screen" />
        <?endif;?>
            
        <?if(get_data('hasMedia')):?>
            <link rel="stylesheet" type="text/css" href="<?=get_data('ctx_qtiDefaultRenderer_lib_www')?>lib/mediaelement/css/mediaelementplayer.min.css" media="screen" />
        <?endif;?>
        <link rel="stylesheet" type="text/css" href="<?=get_data('ctx_taobase_www')?>css/custom-theme/jquery-ui-1.8.22.custom.css" />

        <!-- user CSS -->
        <?foreach(get_data('stylesheets') as $stylesheet):?>
            <link rel="stylesheet" type="text/css" href="<?=$stylesheet['href']?>" media="<?=$stylesheet['media']?>" />
        <?endforeach?>
            
        <script id="initQtiRunner" type="text/javascript">
        (function(){
            window.tao = window.tao || {};
            window.tao.qtiRunnerContext = {
               ctx                 : <?=json_encode(get_data('runtimeContext'))?>,
               itemData            : <?=json_encode(get_data('itemData'))?>,
               variableElements    : <?=json_encode(get_data('contentVariableElements'))?>,
               userVars            : <?=json_encode(get_data('js_variables'))?>,
               customScripts       : <?=json_encode(get_data('javascripts'))?>
           };
        }());
        </script>
            
    <?if(tao_helpers_Mode::is('production')):?>
            <script type="text/javascript" src="<?=get_data('ctx_base_www')?>js/runtime/qtiLoader.min.js"></script>
     <?else:?>
            <script type="text/javascript" 
                    src="<?=get_data('ctx_taobase_www')?>js/lib/require.js"
                    data-main="<?=get_data('ctx_base_www')?>js/runtime/qtiLoader">
            </script>
    <?endif;?>

    </head>
    <body>
        <div id="qti_item"></div>
        <div class="qti_control">
            <?if(get_data('ctx_raw_preview')):?>
                <a href="#" id="qti_validate" style="visibility:hidden;"><?=__("Submit");?></a>
            <?else:?>
                <a href="#" id="qti_validate"><?=__("Submit");?></a>
            <?endif?>
        </div>
        <div id="modalFeedbacks"></div>
    </body>
</html>

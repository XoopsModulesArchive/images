<?php

// $Id: main.php,v 1.11 2003/07/08 12:38:09 okazu Exp $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://xoopscube.org>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
// Author: Kazumi Ono (AKA onokazu)                                          //
// URL: http://www.myweb.ne.jp/, http://xoopscube.org/, http://jp.xoops.org/ //
// Project: The XOOPS Project                                                //
// ------------------------------------------------------------------------- //

if (!is_object($xoopsUser) || !is_object($xoopsModule) || !$xoopsUser->isAdmin($xoopsModule->mid())) {
    exit('Access Denied');
}
    $op = 'list';
    if (isset($_POST)) {
        foreach ($_POST as $k => $v) {
            ${$k} = $v;
        }
    }
    if (isset($_GET['op'])) {
        $op = trim($_GET['op']);
    }
    if (isset($_GET['image_id'])) {
        $image_id = (int)$_GET['image_id'];
    }
    if (isset($_GET['imgcat_id'])) {
        $imgcat_id = (int)$_GET['imgcat_id'];
    }
    if ('list' == $op) {
        $imgcatHandler = xoops_getHandler('imagecategory');

        $imagecategorys = &$imgcatHandler->getObjects();

        xoops_cp_header();

        echo '<h4 style="text-align:left">' . _IMGMANAGER . '</h4><ul>';

        $catcount = count($imagecategorys);

        $imageHandler = xoops_getHandler('image');

        for ($i = 0; $i < $catcount; $i++) {
            $count = $imageHandler->getCount(new Criteria('imgcat_id', $imagecategorys[$i]->getVar('imgcat_id')));

            echo '<li>'
                 . $imagecategorys[$i]->getVar('imgcat_name')
                 . ' ('
                 . sprintf(_NUMIMAGES, '<b>' . $count . '</b>')
                 . ') [<a href="admin.php?fct=images&amp;op=listimg&amp;imgcat_id='
                 . $imagecategorys[$i]->getVar('imgcat_id')
                 . '">'
                 . _LIST
                 . '</a>] [<a href="admin.php?fct=images&amp;op=editcat&amp;imgcat_id='
                 . $imagecategorys[$i]->getVar('imgcat_id')
                 . '">'
                 . _EDIT
                 . '</a>]';

            if ('C' == $imagecategorys[$i]->getVar('imgcat_type')) {
                echo ' [<a href="admin.php?fct=images&amp;op=delcat&amp;imgcat_id=' . $imagecategorys[$i]->getVar('imgcat_id') . '">' . _DELETE . '</a>]';
            }

            echo '</li>';
        }

        echo '</ul>';

        require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

        if (!empty($catcount)) {
            $form = new XoopsThemeForm(_ADDIMAGE, 'image_form', 'admin.php');

            $form->setExtra('enctype="multipart/form-data"');

            $form->addElement(new XoopsFormText(_IMAGENAME, 'image_nicename', 50, 255), true);

            $select = new XoopsFormSelect(_IMAGECAT, 'imgcat_id');

            $select->addOptionArray($imgcatHandler->getList());

            $form->addElement($select, true);

            $form->addElement(new XoopsFormFile(_IMAGEFILE, 'image_file', 5000000));

            $form->addElement(new XoopsFormText(_IMGWEIGHT, 'image_weight', 3, 4, 0));

            $form->addElement(new XoopsFormRadioYN(_IMGDISPLAY, 'image_display', 1, _YES, _NO));

            $form->addElement(new XoopsFormHidden('op', 'addfile'));

            $form->addElement(new XoopsFormHidden('fct', 'images'));

            $form->addElement(new XoopsFormButton('', 'img_button', _SUBMIT, 'submit'));

            $form->display();
        }

        $form = new XoopsThemeForm(_MD_ADDIMGCAT, 'imagecat_form', 'admin.php');

        $form->addElement(new XoopsFormText(_MD_IMGCATNAME, 'imgcat_name', 50, 255), true);

        $form->addElement(new XoopsFormSelectGroup(_MD_IMGCATRGRP, 'readgroup', true, XOOPS_GROUP_ADMIN, 5, true));

        $form->addElement(new XoopsFormSelectGroup(_MD_IMGCATWGRP, 'writegroup', true, XOOPS_GROUP_ADMIN, 5, true));

        $form->addElement(new XoopsFormText(_IMGMAXSIZE, 'imgcat_maxsize', 10, 10, 50000));

        $form->addElement(new XoopsFormText(_IMGMAXWIDTH, 'imgcat_maxwidth', 3, 4, 120));

        $form->addElement(new XoopsFormText(_IMGMAXHEIGHT, 'imgcat_maxheight', 3, 4, 120));

        $form->addElement(new XoopsFormText(_MD_IMGCATWEIGHT, 'imgcat_weight', 3, 4, 0));

        $form->addElement(new XoopsFormRadioYN(_MD_IMGCATDISPLAY, 'imgcat_display', 1, _YES, _NO));

        $storetype = new XoopsFormRadio(_MD_IMGCATSTRTYPE . '<br><span style="color:#ff0000;">' . _MD_STRTYOPENG . '</span>', 'imgcat_storetype', 'file');

        $storetype->addOptionArray(['file' => _MD_ASFILE, 'db' => _MD_INDB]);

        $form->addElement($storetype);

        $form->addElement(new XoopsFormHidden('op', 'addcat'));

        $form->addElement(new XoopsFormHidden('fct', 'images'));

        $form->addElement(new XoopsFormButton('', 'imgcat_button', _SUBMIT, 'submit'));

        $form->display();

        xoops_cp_footer();

        exit();
    }

    if ('listimg' == $op) {
        $imgcat_id = (int)$imgcat_id;

        if ($imgcat_id <= 0) {
            redirect_header('admin.php?fct=images', 1);
        }

        $imgcatHandler = xoops_getHandler('imagecategory');

        $imagecategory = $imgcatHandler->get($imgcat_id);

        if (!is_object($imagecategory)) {
            redirect_header('admin.php?fct=images', 1);
        }

        $imageHandler = xoops_getHandler('image');

        xoops_cp_header();

        echo '<a href="admin.php?fct=images">' . _MD_IMGMAIN . '</a>&nbsp;<span style="font-weight:bold;">&raquo;&raquo;</span>&nbsp;' . $imagecategory->getVar('imgcat_name') . '<br><br>';

        $criteria = new Criteria('imgcat_id', $imgcat_id);

        $imgcount = $imageHandler->getCount($criteria);

        $start = isset($_GET['start']) ? (int)$_GET['start'] : 0;

        $criteria->setStart($start);

        $criteria->setLimit(20);

        $images = &$imageHandler->getObjects($criteria, true, false);

        echo '<form action="admin.php" method="post">';

        foreach (array_keys($images) as $i) {
            echo '<table width="100%" class="outer"><tr><td width="30%" rowspan="6">';

            if ('db' == $imagecategory->getVar('imgcat_storetype')) {
                echo '<img src="' . XOOPS_URL . '/image.php?id=' . $i . '" alt="">';
            } else {
                echo '<img src="' . XOOPS_UPLOAD_URL . '/' . $images[$i]->getVar('image_name') . '" alt="">';
            }

            echo '</td><td class="head">' . _IMAGENAME, '</td><td class="even"><input type="hidden" name="image_id[]" value="'
                                                        . $i
                                                        . '"><input type="text" name="image_nicename[]" value="'
                                                        . $images[$i]->getVar('image_nicename', 'E')
                                                        . '" size="20" maxlength="255"></td></tr><tr><td class="head">'
                                                        . _IMAGEMIME
                                                        . '</td><td class="odd">'
                                                        . $images[$i]->getVar('image_mimetype')
                                                        . '</td></tr><tr><td class="head">'
                                                        . _IMAGECAT
                                                        . '</td><td class="even"><select name="imgcat_id[]" size="1">';

            $list = &$imgcatHandler->getList([], null, null, $imagecategory->getVar('imgcat_storetype'));

            foreach ($list as $value => $name) {
                $sel = '';

                if ($value == $images[$i]->getVar('imgcat_id')) {
                    $sel = ' selected="selected"';
                }

                echo '<option value="' . $value . '"' . $sel . '>' . $name . '</option>';
            }

            echo '</select></td></tr><tr><td class="head">'
                 . _IMGWEIGHT
                 . '</td><td class="odd"><input type="text" name="image_weight[]" value="'
                 . $images[$i]->getVar('image_weight')
                 . '" size="3" maxlength="4"></td></tr><tr><td class="head">'
                 . _IMGDISPLAY
                 . '</td><td class="even"><input type="checkbox" name="image_display[]" value="1"';

            if (1 == $images[$i]->getVar('image_display')) {
                echo ' checked';
            }

            echo '></td></tr><tr><td class="head">&nbsp;</td><td class="odd"><a href="admin.php?fct=images&amp;op=delfile&amp;image_id=' . $i . '">' . _DELETE . '</a></td></tr></table><br>';
        }

        if ($imgcount > 0) {
            if ($imgcount > 20) {
                require_once XOOPS_ROOT_PATH . '/class/pagenav.php';

                $nav = new XoopsPageNav($imgcount, 20, $start, 'start', 'fct=images&amp;op=listimg&amp;imgcat_id=' . $imgcat_id);

                echo '<div text-align="right">' . $nav->renderNav() . '</div>';
            }

            echo '<div style="text-align:center;"><input type="hidden" name="op" value="save"><input type="hidden" name="fct" value="images"><input type="submit" name="submit" value="' . _SUBMIT . '"></div></form>';
        }

        xoops_cp_footer();

        exit();
    }

    if ('save' == $op) {
        $count = count($image_id);

        if ($count > 0) {
            $imageHandler = xoops_getHandler('image');

            $error = [];

            for ($i = 0; $i < $count; $i++) {
                $image = $imageHandler->get($image_id[$i]);

                if (!is_object($image)) {
                    $error[] = sprintf(_FAILGETIMG, $image_id[$i]);

                    continue;
                }

                $image_display[$i] = empty($image_display[$i]) ? 0 : 1;

                $image->setVar('image_display', $image_display[$i]);

                $image->setVar('image_weight', $image_weight[$i]);

                $image->setVar('image_nicename', $image_nicename[$i]);

                $image->setVar('imgcat_id', $imgcat_id[$i]);

                if (!$imageHandler->insert($image)) {
                    $error[] = sprintf(_FAILSAVEIMG, $image_id[$i]);
                }
            }

            if (count($error) > 0) {
                xoops_cp_header();

                foreach ($error as $err) {
                    echo $err . '<br>';
                }

                xoops_cp_footer();

                exit();
            }
        }

        redirect_header('admin.php?fct=images', 2, _MD_AM_DBUPDATED);
    }

    if ('addfile' == $op) {
        $imgcatHandler = xoops_getHandler('imagecategory');

        $imagecategory = $imgcatHandler->get((int)$imgcat_id);

        if (!is_object($imagecategory)) {
            redirect_header('admin.php?fct=images', 1);
        }

        require_once XOOPS_ROOT_PATH . '/class/uploader.php';

        $uploader = new XoopsMediaUploader(XOOPS_UPLOAD_PATH, ['image/gif', 'image/jpeg', 'image/pjpeg', 'image/x-png', 'image/png', 'image/bmp'], $imagecategory->getVar('imgcat_maxsize'), $imagecategory->getVar('imgcat_maxwidth'), $imagecategory->getVar('imgcat_maxheight'));

        $uploader->setPrefix('img');

        $err = [];

        $ucount = count($_POST['xoops_upload_file']);

        for ($i = 0; $i < $ucount; $i++) {
            if ($uploader->fetchMedia($_POST['xoops_upload_file'][$i])) {
                if (!$uploader->upload()) {
                    $err[] = $uploader->getErrors();
                } else {
                    $imageHandler = xoops_getHandler('image');

                    $image = $imageHandler->create();

                    $image->setVar('image_name', $uploader->getSavedFileName());

                    $image->setVar('image_nicename', $image_nicename);

                    $image->setVar('image_mimetype', $uploader->getMediaType());

                    $image->setVar('image_created', time());

                    $image_display = empty($image_display) ? 0 : 1;

                    $image->setVar('image_display', $image_display);

                    $image->setVar('image_weight', $image_weight);

                    $image->setVar('imgcat_id', $imgcat_id);

                    if ('db' == $imagecategory->getVar('imgcat_storetype')) {
                        $fp = @fopen($uploader->getSavedDestination(), 'rb');

                        $fbinary = @fread($fp, filesize($uploader->getSavedDestination()));

                        @fclose($fp);

                        $image->setVar('image_body', $fbinary, true);

                        @unlink($uploader->getSavedDestination());
                    }

                    if (!$imageHandler->insert($image)) {
                        $err[] = sprintf(_FAILSAVEIMG, $image->getVar('image_nicename'));
                    }
                }
            } else {
                $err[] = sprintf(_FAILFETCHIMG, $i);
            }
        }

        if (count($err) > 0) {
            xoops_cp_header();

            xoops_error($err);

            xoops_cp_footer();

            exit();
        }

        redirect_header('admin.php?fct=images', 2, _MD_AM_DBUPDATED);
    }

    if ('addcat' == $op) {
        $imgcatHandler = xoops_getHandler('imagecategory');

        $imagecategory = $imgcatHandler->create();

        $imagecategory->setVar('imgcat_name', $imgcat_name);

        $imagecategory->setVar('imgcat_maxsize', $imgcat_maxsize);

        $imagecategory->setVar('imgcat_maxwidth', $imgcat_maxwidth);

        $imagecategory->setVar('imgcat_maxheight', $imgcat_maxheight);

        $imgcat_display = empty($imgcat_display) ? 0 : 1;

        $imagecategory->setVar('imgcat_display', $imgcat_display);

        $imagecategory->setVar('imgcat_weight', $imgcat_weight);

        $imagecategory->setVar('imgcat_storetype', $imgcat_storetype);

        $imagecategory->setVar('imgcat_type', 'C');

        if (!$imgcatHandler->insert($imagecategory)) {
            exit();
        }

        $newid = $imagecategory->getVar('imgcat_id');

        $imagecategorypermHandler = xoops_getHandler('groupperm');

        if (!isset($readgroup)) {
            $readgroup = [];
        }

        if (!in_array(XOOPS_GROUP_ADMIN, $readgroup, true)) {
            $readgroup[] = XOOPS_GROUP_ADMIN;
        }

        foreach ($readgroup as $rgroup) {
            $imagecategoryperm = $imagecategorypermHandler->create();

            $imagecategoryperm->setVar('gperm_groupid', $rgroup);

            $imagecategoryperm->setVar('gperm_itemid', $newid);

            $imagecategoryperm->setVar('gperm_name', 'imgcat_read');

            $imagecategoryperm->setVar('gperm_modid', 1);

            $imagecategorypermHandler->insert($imagecategoryperm);

            unset($imagecategoryperm);
        }

        if (!isset($writegroup)) {
            $writegroup = [];
        }

        if (!in_array(XOOPS_GROUP_ADMIN, $writegroup, true)) {
            $writegroup[] = XOOPS_GROUP_ADMIN;
        }

        foreach ($writegroup as $wgroup) {
            $imagecategoryperm = $imagecategorypermHandler->create();

            $imagecategoryperm->setVar('gperm_groupid', $wgroup);

            $imagecategoryperm->setVar('gperm_itemid', $newid);

            $imagecategoryperm->setVar('gperm_name', 'imgcat_write');

            $imagecategoryperm->setVar('gperm_modid', 1);

            $imagecategorypermHandler->insert($imagecategoryperm);

            unset($imagecategoryperm);
        }

        redirect_header('admin.php?fct=images', 2, _MD_AM_DBUPDATED);
    }

    if ('editcat' == $op) {
        if ($imgcat_id <= 0) {
            redirect_header('admin.php?fct=images', 1);
        }

        $imgcatHandler = xoops_getHandler('imagecategory');

        $imagecategory = $imgcatHandler->get($imgcat_id);

        if (!is_object($imagecategory)) {
            redirect_header('admin.php?fct=images', 1);
        }

        require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

        $imagecategorypermHandler = xoops_getHandler('groupperm');

        $form = new XoopsThemeForm(_MD_EDITIMGCAT, 'imagecat_form', 'admin.php');

        $form->addElement(new XoopsFormText(_MD_IMGCATNAME, 'imgcat_name', 50, 255, $imagecategory->getVar('imgcat_name')), true);

        $form->addElement(new XoopsFormSelectGroup(_MD_IMGCATRGRP, 'readgroup', true, $imagecategorypermHandler->getGroupIds('imgcat_read', $imgcat_id), 5, true));

        $form->addElement(new XoopsFormSelectGroup(_MD_IMGCATWGRP, 'writegroup', true, $imagecategorypermHandler->getGroupIds('imgcat_write', $imgcat_id), 5, true));

        $form->addElement(new XoopsFormText(_IMGMAXSIZE, 'imgcat_maxsize', 10, 10, $imagecategory->getVar('imgcat_maxsize')));

        $form->addElement(new XoopsFormText(_IMGMAXWIDTH, 'imgcat_maxwidth', 3, 4, $imagecategory->getVar('imgcat_maxwidth')));

        $form->addElement(new XoopsFormText(_IMGMAXHEIGHT, 'imgcat_maxheight', 3, 4, $imagecategory->getVar('imgcat_maxheight')));

        $form->addElement(new XoopsFormText(_MD_IMGCATWEIGHT, 'imgcat_weight', 3, 4, $imagecategory->getVar('imgcat_weight')));

        $form->addElement(new XoopsFormRadioYN(_MD_IMGCATDISPLAY, 'imgcat_display', $imagecategory->getVar('imgcat_display'), _YES, _NO));

        $storetype = ['db' => _MD_INDB, 'file' => _MD_ASFILE];

        $form->addElement(new XoopsFormLabel(_MD_IMGCATSTRTYPE, $storetype[$imagecategory->getVar('imgcat_storetype')]));

        $form->addElement(new XoopsFormHidden('imgcat_id', $imgcat_id));

        $form->addElement(new XoopsFormHidden('op', 'updatecat'));

        $form->addElement(new XoopsFormHidden('fct', 'images'));

        $form->addElement(new XoopsFormButton('', 'imgcat_button', _SUBMIT, 'submit'));

        xoops_cp_header();

        echo '<a href="admin.php?fct=images">' . _MD_IMGMAIN . '</a>&nbsp;<span style="font-weight:bold;">&raquo;&raquo;</span>&nbsp;' . $imagecategory->getVar('imgcat_name') . '<br><br>';

        $form->display();

        xoops_cp_footer();

        exit();
    }

    if ('updatecat' == $op) {
        if ($imgcat_id <= 0) {
            redirect_header('admin.php?fct=images', 1);
        }

        $imgcatHandler = xoops_getHandler('imagecategory');

        $imagecategory = $imgcatHandler->get($imgcat_id);

        if (!is_object($imagecategory)) {
            redirect_header('admin.php?fct=images', 1);
        }

        $imagecategory->setVar('imgcat_name', $imgcat_name);

        $imgcat_display = empty($imgcat_display) ? 0 : 1;

        $imagecategory->setVar('imgcat_display', $imgcat_display);

        $imagecategory->setVar('imgcat_maxsize', $imgcat_maxsize);

        $imagecategory->setVar('imgcat_maxwidth', $imgcat_maxwidth);

        $imagecategory->setVar('imgcat_maxheight', $imgcat_maxheight);

        $imagecategory->setVar('imgcat_weight', $imgcat_weight);

        if (!$imgcatHandler->insert($imagecategory)) {
            exit();
        }

        $imagecategorypermHandler = xoops_getHandler('groupperm');

        $criteria = new CriteriaCompo(new Criteria('gperm_itemid', $imgcat_id));

        $criteria->add(new Criteria('gperm_modid', 1));

        $criteria2 = new CriteriaCompo(new Criteria('gperm_name', 'imgcat_write'));

        $criteria2->add(new Criteria('gperm_name', 'imgcat_read'), 'OR');

        $criteria->add($criteria2);

        $imagecategorypermHandler->deleteAll($criteria);

        if (!isset($readgroup)) {
            $readgroup = [];
        }

        if (!in_array(XOOPS_GROUP_ADMIN, $readgroup, true)) {
            $readgroup[] = XOOPS_GROUP_ADMIN;
        }

        foreach ($readgroup as $rgroup) {
            $imagecategoryperm = $imagecategorypermHandler->create();

            $imagecategoryperm->setVar('gperm_groupid', $rgroup);

            $imagecategoryperm->setVar('gperm_itemid', $imgcat_id);

            $imagecategoryperm->setVar('gperm_name', 'imgcat_read');

            $imagecategoryperm->setVar('gperm_modid', 1);

            $imagecategorypermHandler->insert($imagecategoryperm);

            unset($imagecategoryperm);
        }

        if (!isset($writegroup)) {
            $writegroup = [];
        }

        if (!in_array(XOOPS_GROUP_ADMIN, $writegroup, true)) {
            $writegroup[] = XOOPS_GROUP_ADMIN;
        }

        foreach ($writegroup as $wgroup) {
            $imagecategoryperm = $imagecategorypermHandler->create();

            $imagecategoryperm->setVar('gperm_groupid', $wgroup);

            $imagecategoryperm->setVar('gperm_itemid', $imgcat_id);

            $imagecategoryperm->setVar('gperm_name', 'imgcat_write');

            $imagecategoryperm->setVar('gperm_modid', 1);

            $imagecategorypermHandler->insert($imagecategoryperm);

            unset($imagecategoryperm);
        }

        redirect_header('admin.php?fct=images', 2, _MD_AM_DBUPDATED);
    }

    if ('delfile' == $op) {
        xoops_cp_header();

        xoops_confirm(['op' => 'delfileok', 'image_id' => $image_id, 'fct' => 'images'], 'admin.php', _MD_RUDELIMG);

        xoops_cp_footer();

        exit();
    }

    if ('delfileok' == $op) {
        $image_id = (int)$image_id;

        if ($image_id <= 0) {
            redirect_header('admin.php?fct=images', 1);
        }

        $imageHandler = xoops_getHandler('image');

        $image = $imageHandler->get($image_id);

        if (!is_object($image)) {
            redirect_header('admin.php?fct=images', 1);
        }

        if (!$imageHandler->delete($image)) {
            xoops_cp_header();

            xoops_error(sprintf(_MD_FAILDEL, $image->getVar('image_id')));

            xoops_cp_footer();

            exit();
        }

        @unlink(XOOPS_UPLOAD_PATH . '/' . $image->getVar('image_name'));

        redirect_header('admin.php?fct=images', 2, _MD_AM_DBUPDATED);
    }

    if ('delcat' == $op) {
        xoops_cp_header();

        xoops_confirm(['op' => 'delcatok', 'imgcat_id' => $imgcat_id, 'fct' => 'images'], 'admin.php', _MD_RUDELIMGCAT);

        xoops_cp_footer();

        exit();
    }

    if ('delcatok' == $op) {
        $imgcat_id = (int)$imgcat_id;

        if ($imgcat_id <= 0) {
            redirect_header('admin.php?fct=images', 1);
        }

        $imgcatHandler = xoops_getHandler('imagecategory');

        $imagecategory = $imgcatHandler->get($imgcat_id);

        if (!is_object($imagecategory)) {
            redirect_header('admin.php?fct=images', 1);
        }

        if ('C' != $imagecategory->getVar('imgcat_type')) {
            xoops_cp_header();

            xoops_error(_MD_SCATDELNG);

            xoops_cp_footer();

            exit();
        }

        $imageHandler = xoops_getHandler('image');

        $images = &$imageHandler->getObjects(new Criteria('imgcat_id', $imgcat_id), true, false);

        $errors = [];

        foreach (array_keys($images) as $i) {
            if (!$imageHandler->delete($images[$i])) {
                $errors[] = sprintf(_MD_FAILDEL, $i);
            } else {
                if (file_exists(XOOPS_UPLOAD_PATH . '/' . $images[$i]->getVar('image_name')) && !unlink(XOOPS_UPLOAD_PATH . '/' . $images[$i]->getVar('image_name'))) {
                    $errors[] = sprintf(_MD_FAILUNLINK, $i);
                }
            }
        }

        if (!$imgcatHandler->delete($imagecategory)) {
            $errors[] = sprintf(_MD_FAILDELCAT, $imagecategory->getVar('imgcat_name'));
        }

        if (count($errors) > 0) {
            xoops_cp_header();

            xoops_error($errors);

            xoops_cp_footer();

            exit();
        }

        redirect_header('admin.php?fct=images', 2, _MD_AM_DBUPDATED);
    }

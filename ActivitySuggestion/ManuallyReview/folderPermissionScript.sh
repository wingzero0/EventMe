#!/bin/bash
# it will create subfolder in ArticleXXX and change the folder attribute in g+w
# usage:
# bash folderPermissionScript.sh newFolderName


newSiteSource=$1

mkdir "ArticleTmp/$newSiteSource/"
mkdir "ArticleComplete/$newSiteSource/"
mkdir "ArticleSkip/$newSiteSource/"

chmod g+w -R Article*

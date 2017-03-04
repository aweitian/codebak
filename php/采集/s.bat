@echo off

if [%1]==[] goto usage

set PATH=%PATH%;C:\xampp\php
if not exist cache mkdir cache
if not exist cache/fetch cd cache && mkdir fetch && cd ..
if not exist output/%1 cd output & mkdir %1 && cd ..
if not exist cache/.feed echo  > cache/.feed
if not exist cache/.fetch echo  > cache/.fetch
if not exist cache/.filter echo  > cache/.filter
if not exist cache/.save echo  > cache/.save

echo 1 > cache/.count
echo %1 > cache/.m

if not exist feed/%1.php goto error_feed
if not exist fetch/%1.php goto error_fetch
if not exist filter/%1.php goto error_filter
if not exist save/%1.php goto error_save


for /f %%c in (raw.txt) do (
	php lib/init.php & echo %%c > cache/.feed & php feed/%1.php & php fetch/%1.php & php filter/%1.php & php save/%1.php
)



echo ---------------------------------------------
echo done...
goto end



:usage
	echo usage:
	echo 	s feed fetch straphtml append
	goto end

:error_feed
echo feed/%1 is nonexist
goto end

:error_fetch
echo fetch/%1 is nonexist
goto end


:error_filter
echo filter/%1 is nonexist
goto end

:error_save
echo save/%1 is nonexist
goto end




:end

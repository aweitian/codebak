rem @echo off
set b=%cd%
c:
cd c:\xampp
rd htdocs
mklink /D /J htdocs %b%
%b:~,1%:
cd %b%
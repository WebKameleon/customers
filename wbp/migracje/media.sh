php="include 'const.php'; echo \$kameleon_prefix;"
kameleon=`php -r "$php"`
cd `dirname $0`
migra=`pwd`



cd $kameleon/media/wbpicak
tar -czf $migra/media.tgz --newer="`date +'%D %T' --reference=$migra/media.last`" images files
scp $migra/media.tgz root@szafir.gammanet.pl:/mnt/big/jails/upload/home/cmspremium/media/wbpicak
ssh root@szafir.gammanet.pl /mnt/big/jails/upload/home/cmspremium/media/wbpicak/media.sh
#scp root@szafir.gammanet.pl:/mnt/big/jails/upload/home/cmspremium/media/wbpicak/template.tgz .

rm $migra/media.tgz
touch $migra/media.last

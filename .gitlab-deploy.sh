
cd /staging/admin && git pull origin staging
rsync -ravzh -e 'ssh -p 22' --chown=ec2-user:ec2-user --delete --exclude=".git*" --exclude=".env" --exclude="inc/config.php" --exclude="*.sh" --exclude="README.md"  --exclude="src/i18n/*" --exclude="bo/*" --exclude="framework/*" --exclude="test.php" --exclude=".static" --exclude="wl001/*" --exclude="wl002/*" --exclude="wl003/*" --exclude="wltest/*" /staging/admin/ root@18.166.15.250:/mnt/data/microservice/admin/

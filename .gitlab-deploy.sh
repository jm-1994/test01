














rsync -ravz -e 'ssh -p 22' --chown=centos:centos --delete --exclude=".git*" --exclude=".env" --exclude="inc/config.php" --exclude="*.sh" --exclude="README.md"  --exclude="log" --exclude="log/*" /master/payment_gateway/ root@18.162.246.70:/mnt/data/microservice/finance/PaymentGateway/
rsync -ravz -e 'ssh -p 22' --chown=centos:centos --delete --exclude=".git*" --exclude=".env" --exclude="inc/config.php" --exclude="*.sh" --exclude="README.md"  --exclude="log" --exclude="log/*" /master/payment_gateway/ root@18.166.68.170:/mnt/data/microservice/finance/PaymentGateway/
rsync -ravzO --chown=wlgcldfcftp:wlgcldfcftp --delete --exclude=".git*" --exclude=".env" --exclude="inc/config.php" --exclude="*.sh" --exclude="README.md"  --exclude="log" --exclude="log/*" /master/payment_gateway/ wlfilesync@107.167.186.30:/mnt/data/microservice/finance/PaymentGateway/


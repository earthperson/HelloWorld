#!/bin/bash
user_id=1
password='202cb962ac59075b964b07152d234b70'
cmd='send_sms'
da=''
text=`echo "Some text here" | openssl base64`
text_encoded='base64'
varhash=`echo -n "${user_id}&${cmd}&${da}&${text}&${password}" | md5sum`
post_data="{\"user_id\":\"$user_id\", \"cmd\":\"$cmd\", \"da\":\"$da\", \"text\":\"$text\", \"textEncoded\":\"$text_encoded\", \"hash\":\"${varhash:0:32}\"}"
echo $post_data

R=`wget --post-data="$post_data" -O /dev/stdout -q http://192.168.1.198/gateway.php`
echo $R

#!/bin/bash

# Form this URL using the Bomgar API to send sessions to the appropriate place.
# Alternatively, you can prompt the customer for a session key here and use it as
# a paramter to start a session
url=$1
userAgent="Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_1) AppleWebKit/534.48.3 (KHTML, like Gecko) Version/5.1 Safari/534.48.3"

# Get the filename using one request
filename=$(/usr/bin/curl -A "$userAgent" -L -k -sI $url | /usr/bin/grep -o -E 'filename=.*$' | /usr/bin/sed -e 's/filename=//') 
filename=`/bin/echo "${filename%?}"`

# Download the file with the proper name
/usr/bin/curl -A "$userAgent" -k -o "tmp.bgr" -L $url

# Execute the newly downloaded file
/bin/mv tmp.bgr "$filename"
/usr/bin/unzip $filename

exec "Double-Click To Start Support Session.app/Contents/MacOS/mount_dmg_and_exec_bundle.sh"
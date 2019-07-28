<?php
$string = '{
  "type": "message",
  "locale": "en-Us",
  "channelID": "email",
  "from": { "id": "ittrjeu@gmail.com", "name": "My bot"},
  "recipient": { "id": "congtac.net@gmail.com", "name": "Joe Doe"},
  "conversation": { "id": "123123123123", "topic": "awesome chat" },
  "channelData": { 
    "htmlBody": "<html><body style = \"font-family: Calibri; font-size: 11pt;\" >This is more than awesome.</body></html>",
    "subject": "Super awesome message subject",
    "importance": "high",
    "ccRecipients": "abcwebclass@gmail.com;congtrieuremember@yahoo.com"
  }
}';
print_r(json_decode($string,true));
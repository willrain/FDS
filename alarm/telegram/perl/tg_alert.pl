#!/usr/bin/perl
# by alex 2014/10/06 
use Expect;
use warnings;
use Config::IniFiles;
use strict;
my $GROUP="ADMIN_SMS";
my $file = "./config.ini";
my $ini = Config::IniFiles->new(
   -file    => $file
   ) or die "Could not open $file!";
my $SMS_SEND_USER=$ini->val($GROUP,'ADMIN' );
my @SMS_SEND_USER_ARRAY=split(/,/, $SMS_SEND_USER);
my $CHATING_NAME=$ini->val($GROUP,'CHAT_NAME' );
my ($MESG, $n) = @ARGV;
if (not defined $MESG) {
exit ;
}
for( ; ; )
{
my $script = "/data02/sw/telegram/bin/telegram -W -k  /etc/telegram/server.pub ";
my $command = Expect->spawn($script);
my $b="1:TIMEOUT";
$command->log_stdout(1);
my $timeout= 3;
while (
$command->expect($timeout,
               -re => '>', 
            ) ){
       $command->clear_accum();
               if( $command->exp_match_number == 1 ){
                foreach my $USER (@SMS_SEND_USER_ARRAY)
                     {
                       print $command "chat_add_user $CHATING_NAME $USER\r\n";
                    }
                       print $command "msg $CHATING_NAME $MESG\r\n";
                       print $command "quit\r\n";
               } 
         }
my $error = $command->exp_error();
   if($error=~/$b/)  {
        print "fail !!\n";
        print "Re-send MESG !!\n";
    }
   else {
         print "ok.\n";
   last;
 }
}


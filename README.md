# An analytic website for golf at the moment

71EB90B4D10111E89F328F0D91AFBA99

Admin X3


 
 carbonphp.com
 
 


 php index.php rest -s StatsCoach -p 'Huskies!99' -json 
 
    static $count;
 
         null === $count and $count = 0;
 
         if (++$count > 1 ){
             sortDump('started app twice');
         };
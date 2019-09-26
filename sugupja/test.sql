select m03_name, m03_injung_to
, date_add(date_format(m03_injung_to, '%Y-%m-%d'), interval -3 month)
, date_format(now(), '%Y-%m-%d')
, datediff( date_add(date_format(m03_injung_to, '%Y-%m-%d'), interval -3 month), date_format(now(), '%Y-%m-%d'))
  from m03sugupja  
 where m03_ccode = '1234'
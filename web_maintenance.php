<?php
if(isset($web_down) && $web_down==1){
?>
<DIV align=center>
<CENTER>
<img src="http://app.adhyayan.asia/public/images/logo.png" alt="Logo - Adhyayan">    
<TABLE style="BORDER-COLLAPSE: collapse" borderColor=#111111 cellSpacing=0 
cellPadding=0 width="75%" border="0">
  <TBODY>
  <TR>
    <TD align=middle height=100><FONT face=Verdana>&nbsp;</FONT></TD></TR>
  <TR>
    <TD align=middle>
      <P align=middle><B><FONT face=Verdana color=#ff0000 size=4>Adhyayan software is under 
      Service !!!</FONT></B></P></TD></TR>
  <TR>
    <TD align=middle><FONT face=Verdana>&nbsp;</FONT></TD></TR>
  <TR>
    <TD align=middle>
      <P align=center><FONT face=Verdana><?php echo isset($web_maintenance_msg)?$web_maintenance_msg:'Due to the upgrade and maintenance 
      process, our web site will not be available at the moment, we apologize 
      for the inconvenience and the site will be fully accessible soon.'; ?></FONT> 
  </P></TD></TR></TBODY></TABLE></CENTER></DIV>
<?php
}
?>


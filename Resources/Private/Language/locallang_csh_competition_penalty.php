<?php
$LOCAL_LANG = Array(
  'default' => Array(
    '.description' => '
      A competition penalty is a record to perform corrections on a league table.',
    'correction.description' => 'Mark this record as a table correction. Not handled as penalty',
    'correction.details' => '
<h2>When to set this marker?</h2>
<p>Use it, if you do not want to mark this penalty record as a penalty in FE. Sometimes you simply want to 
correct a league table. Maybe some teams start with a predefined number of points.
</p>
    ',
  ),


  'de' => Array(
    '.description' => '
      Mit der Wettbewerbsstrafe können Korrekturen an der Ligatabelle durchgeführt werden.',
    'correction.description' => 'Tabellenkorrektur, keine Strafe.',
    'correction.details' => '
<h2>Was ist der Unterschied?</h2>
<p>Bei einer Strafe wird im Frontend üblicherweise das Team markiert und es wird ein entsprechender Hinweis
angezeigt. Wenn man dies vermeiden will, weil z.B. die Teams mit einem bestimmten Startpunktzahl in die Liga
starten, dann muss man dieses Flag setzen.
</p>
    ',
  ),
);
?>
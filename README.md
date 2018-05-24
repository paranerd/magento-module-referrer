Registrierung:
	(1) Werber-ID angegeben: Zuordnung entsprechend
	(2) Keine Werber-ID: Reihum Zuordnung zu ausgewählten Hausmitarbeitern

Prämienberechnung:
	(1) In Stufe 0 keine Prämien aus Gruppenumsätzen (da alle auf der gleichen Stufe sind)
	(2) Stufen-Prozente auf EIGEN-Umsatz
	(3) Differenz von Prozenten der eigenen Stufe zu Prozenten des jeweiligen Kind-Knotens auf Umsatz des Kind-Clusters

Prüfung Stufenaufstieg:
	(0) Stufe 0 -> Stufe 1: wurden 2.500€ Umsatz erreicht? (zeitlich unbegrenzt)
	(1) Darüber: halbjährlich fix 01.01. - 30.06. - 31.12. prüfen
	(2) Erforderlichen Umsatz für nächste Stufe ermitteln
	(3) Umsatz der Kind-Knoten ermitteln (jeweils max. 50% vom erforderlichen Umsatz)
	(4) Eigenumsatz in voller Höhe

Stufenaufstieg (Eltern-Anpassung):
	- Wenn Kind-Level < Eltern-Level sein muss (aus programmiertechnischer Sicht empfehlenswert): Custom_ID für das Referring erst ab Level 1 anzeigen!
	- Referring bei Level 0 macht auch keinen Sinn, weil Kinder nicht zum Bonus beitragen
	- Wenn Kind-Level == Eltern-Level möglich wäre, könnte ein ganzer Strang aus Kunden mit z.B. Level 1 bestehen
	- Ein Level-Up bei Kind-Level == Eltern-Level möglich könnte dazu führen, dass viele Ebenen übersprungen werden (Kind Level 0 -> Level 1, Eltern Level 1, Großeltern Level 1)

Button template in module-backend/view/adminhtml/templates/dashboard/totalbar/refreshstatistics.phtml

# Registrierung
  - Kunde bekommt zufällige (einmalige) CustomID zugewiesen
  - Gibt er die CustomID eines Werbers an, wird sein "referrer" auf diese ID gesetzt
  - Falls nicht, wird reihum einer der Mitarbeiter für die Zuweisung ausgewählt

# Dashboard
  - Dem Kunden wird im Bereich "Promotion" seine eigene CustomID angezeigt (mit der er andere werben kann)
  - Außerdem werden ihm seine Stufe, der eigene Umsatz, der Umsatz all seiner hierarchischen Kinder, sowie sein daraus erwachsener Bonus angezeigt
  - Bonus und Stufe werden bei jedem Aufruf aktuell bestimmt
  - Hier ist die Frage, ob der Stufenaufstieg 2x jährlich vom Admin ausgelöst wird, oder ständig nach Ablauf der Storno-Frist geprüft und aktualisiert wird

# Bestellung
  - Nach Abschluss des Bestellvorgangs wird dem Kunden und seinen hierarchischen Eltern ein Bonus entsprechend ihrer Stufe zugewiesen
  - Dies sollte womöglich erst nach Ablauf der Storno-Frist passieren!

# Pending Bonus
	//Im Observer wird $customer->distributePendingBonus() aufgerufen
	Im Observer werden nach Kaufabschluss Kunden-ID und Bestellwert in die pending_bonus-Tabelle eingetragen

	Per Cron werden täglich alle PendingBoni aus der DB gelesen die älter sind, als time() - Storno-Frist
	Dann wird für jeden betroffenen Kunden die $customer->distributeBonus($amount) aufgerufen
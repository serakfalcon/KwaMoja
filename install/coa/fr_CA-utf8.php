<?php
InsertRecord('accountsection',array('sectionid'),array(10),array('sectionid','sectionname'),array(10,'Assets'), $db);
InsertRecord('accountsection',array('sectionid'),array(20),array('sectionid','sectionname'),array(20,'Liabilities'), $db);
InsertRecord('accountsection',array('sectionid'),array(30),array('sectionid','sectionname'),array(30,'Income'), $db);
InsertRecord('accountsection',array('sectionid'),array(40),array('sectionid','sectionname'),array(40,'Costs'), $db);
InsertRecord('accountgroups',array('groupname'),array('ACTIF COURANT'),array('groupname','sectioninaccounts','pandl','sequenceintb','parentgroupname'),array('ACTIF COURANT','10','0','1000',''), $db);
InsertRecord('accountgroups',array('groupname'),array('AUTRES IMMOBILISATIONS'),array('groupname','sectioninaccounts','pandl','sequenceintb','parentgroupname'),array('AUTRES IMMOBILISATIONS','10','0','3000',''), $db);
InsertRecord('accountgroups',array('groupname'),array('AUTRES REVENUS'),array('groupname','sectioninaccounts','pandl','sequenceintb','parentgroupname'),array('AUTRES REVENUS','30','1','9000',''), $db);
InsertRecord('accountgroups',array('groupname'),array('CAPITAL SOCIAL'),array('groupname','sectioninaccounts','pandl','sequenceintb','parentgroupname'),array('CAPITAL SOCIAL','20','0','7000',''), $db);
InsertRecord('accountgroups',array('groupname'),array('CO'),array('groupname','sectioninaccounts','pandl','sequenceintb','parentgroupname'),array('CO','40','1','10000',''), $db);
InsertRecord('accountgroups',array('groupname'),array('D'),array('groupname','sectioninaccounts','pandl','sequenceintb','parentgroupname'),array('D','40','1','12000',''), $db);
InsertRecord('accountgroups',array('groupname'),array('FRAIS DE PERSONNEL'),array('groupname','sectioninaccounts','pandl','sequenceintb','parentgroupname'),array('FRAIS DE PERSONNEL','40','1','11000',''), $db);
InsertRecord('accountgroups',array('groupname'),array('PASSIF'),array('groupname','sectioninaccounts','pandl','sequenceintb','parentgroupname'),array('PASSIF','20','0','6000',''), $db);
InsertRecord('accountgroups',array('groupname'),array('PASSIF COURANT'),array('groupname','sectioninaccounts','pandl','sequenceintb','parentgroupname'),array('PASSIF COURANT','20','0','4000',''), $db);
InsertRecord('accountgroups',array('groupname'),array('RETENUES SUR SALAIRE'),array('groupname','sectioninaccounts','pandl','sequenceintb','parentgroupname'),array('RETENUES SUR SALAIRE','20','0','5000',''), $db);
InsertRecord('accountgroups',array('groupname'),array('REVENUS DE VENTE'),array('groupname','sectioninaccounts','pandl','sequenceintb','parentgroupname'),array('REVENUS DE VENTE','30','1','8000',''), $db);
InsertRecord('accountgroups',array('groupname'),array('STOCKS'),array('groupname','sectioninaccounts','pandl','sequenceintb','parentgroupname'),array('STOCKS','10','0','2000',''), $db);
InsertRecord('chartmaster',array('accountcaode'),array('1060'),array('accountcode','accountname','group_'),array('1060','Compte ch','ACTIF COURANT'), $db);
InsertRecord('chartmaster',array('accountcaode'),array('1065'),array('accountcode','accountname','group_'),array('1065','Petite caisse','ACTIF COURANT'), $db);
InsertRecord('chartmaster',array('accountcaode'),array('1200'),array('accountcode','accountname','group_'),array('1200','Comptes clients','ACTIF COURANT'), $db);
InsertRecord('chartmaster',array('accountcaode'),array('1205'),array('accountcode','accountname','group_'),array('1205','Provisions pour cr','ACTIF COURANT'), $db);
InsertRecord('chartmaster',array('accountcaode'),array('1520'),array('accountcode','accountname','group_'),array('1520','Stocks / G','STOCKS'), $db);
InsertRecord('chartmaster',array('accountcaode'),array('1530'),array('accountcode','accountname','group_'),array('1530','Stocks / Pi','STOCKS'), $db);
InsertRecord('chartmaster',array('accountcaode'),array('1540'),array('accountcode','accountname','group_'),array('1540','Stocks / Mati','STOCKS'), $db);
InsertRecord('chartmaster',array('accountcaode'),array('1820'),array('accountcode','accountname','group_'),array('1820','Meubles et accessoires','AUTRES IMMOBILISATIONS'), $db);
InsertRecord('chartmaster',array('accountcaode'),array('1825'),array('accountcode','accountname','group_'),array('1825','Amortissement cumul','AUTRES IMMOBILISATIONS'), $db);
InsertRecord('chartmaster',array('accountcaode'),array('1840'),array('accountcode','accountname','group_'),array('1840','V','AUTRES IMMOBILISATIONS'), $db);
InsertRecord('chartmaster',array('accountcaode'),array('1845'),array('accountcode','accountname','group_'),array('1845','Amortissement cumul','AUTRES IMMOBILISATIONS'), $db);
InsertRecord('chartmaster',array('accountcaode'),array('2100'),array('accountcode','accountname','group_'),array('2100','Comptes fournisseurs','PASSIF COURANT'), $db);
InsertRecord('chartmaster',array('accountcaode'),array('2160'),array('accountcode','accountname','group_'),array('2160','Taxes f','PASSIF COURANT'), $db);
InsertRecord('chartmaster',array('accountcaode'),array('2170'),array('accountcode','accountname','group_'),array('2170','Taxes provinciales','PASSIF COURANT'), $db);
InsertRecord('chartmaster',array('accountcaode'),array('2310'),array('accountcode','accountname','group_'),array('2310','TPS','PASSIF COURANT'), $db);
InsertRecord('chartmaster',array('accountcaode'),array('2320'),array('accountcode','accountname','group_'),array('2320','TVQ','PASSIF COURANT'), $db);
InsertRecord('chartmaster',array('accountcaode'),array('2380'),array('accountcode','accountname','group_'),array('2380','Indemnit','PASSIF COURANT'), $db);
InsertRecord('chartmaster',array('accountcaode'),array('2390'),array('accountcode','accountname','group_'),array('2390','CSST','PASSIF COURANT'), $db);
InsertRecord('chartmaster',array('accountcaode'),array('2410'),array('accountcode','accountname','group_'),array('2410','Assurance-emploi','RETENUES SUR SALAIRE'), $db);
InsertRecord('chartmaster',array('accountcaode'),array('2420'),array('accountcode','accountname','group_'),array('2420','RRQ','RETENUES SUR SALAIRE'), $db);
InsertRecord('chartmaster',array('accountcaode'),array('2450'),array('accountcode','accountname','group_'),array('2450','Imp','RETENUES SUR SALAIRE'), $db);
InsertRecord('chartmaster',array('accountcaode'),array('2620'),array('accountcode','accountname','group_'),array('2620','Emprunts bancaires','PASSIF'), $db);
InsertRecord('chartmaster',array('accountcaode'),array('2680'),array('accountcode','accountname','group_'),array('2680','Emprunt aupr','PASSIF'), $db);
InsertRecord('chartmaster',array('accountcaode'),array('3350'),array('accountcode','accountname','group_'),array('3350','Actions ordinaires','CAPITAL SOCIAL'), $db);
InsertRecord('chartmaster',array('accountcaode'),array('4020'),array('accountcode','accountname','group_'),array('4020','Ventes g','REVENUS DE VENTE'), $db);
InsertRecord('chartmaster',array('accountcaode'),array('4030'),array('accountcode','accountname','group_'),array('4030','Pi','REVENUS DE VENTE'), $db);
InsertRecord('chartmaster',array('accountcaode'),array('4430'),array('accountcode','accountname','group_'),array('4430','Transport et manutention','AUTRES REVENUS'), $db);
InsertRecord('chartmaster',array('accountcaode'),array('4440'),array('accountcode','accountname','group_'),array('4440','Int','AUTRES REVENUS'), $db);
InsertRecord('chartmaster',array('accountcaode'),array('4450'),array('accountcode','accountname','group_'),array('4450','Gain sur change','AUTRES REVENUS'), $db);
InsertRecord('chartmaster',array('accountcaode'),array('5010'),array('accountcode','accountname','group_'),array('5010','Achats','CO'), $db);
InsertRecord('chartmaster',array('accountcaode'),array('5050'),array('accountcode','accountname','group_'),array('5050','Pi','CO'), $db);
InsertRecord('chartmaster',array('accountcaode'),array('5100'),array('accountcode','accountname','group_'),array('5100','Frais de transport','CO'), $db);
InsertRecord('chartmaster',array('accountcaode'),array('5410'),array('accountcode','accountname','group_'),array('5410','Salaires','FRAIS DE PERSONNEL'), $db);
InsertRecord('chartmaster',array('accountcaode'),array('5420'),array('accountcode','accountname','group_'),array('5420','D','FRAIS DE PERSONNEL'), $db);
InsertRecord('chartmaster',array('accountcaode'),array('5430'),array('accountcode','accountname','group_'),array('5430','D','FRAIS DE PERSONNEL'), $db);
InsertRecord('chartmaster',array('accountcaode'),array('5440'),array('accountcode','accountname','group_'),array('5440','D','FRAIS DE PERSONNEL'), $db);
InsertRecord('chartmaster',array('accountcaode'),array('5610'),array('accountcode','accountname','group_'),array('5610','Frais comptables et juridiques','D'), $db);
InsertRecord('chartmaster',array('accountcaode'),array('5615'),array('accountcode','accountname','group_'),array('5615','Publicit','D'), $db);
InsertRecord('chartmaster',array('accountcaode'),array('5620'),array('accountcode','accountname','group_'),array('5620','Cr','D'), $db);
InsertRecord('chartmaster',array('accountcaode'),array('5660'),array('accountcode','accountname','group_'),array('5660','Amortissement de l\'exercice','D'), $db);
InsertRecord('chartmaster',array('accountcaode'),array('5680'),array('accountcode','accountname','group_'),array('5680','Imp','D'), $db);
InsertRecord('chartmaster',array('accountcaode'),array('5685'),array('accountcode','accountname','group_'),array('5685','Assurances','D'), $db);
InsertRecord('chartmaster',array('accountcaode'),array('5690'),array('accountcode','accountname','group_'),array('5690','Int','D'), $db);
InsertRecord('chartmaster',array('accountcaode'),array('5700'),array('accountcode','accountname','group_'),array('5700','Fournitures de bureau','D'), $db);
InsertRecord('chartmaster',array('accountcaode'),array('5760'),array('accountcode','accountname','group_'),array('5760','Loyer','D'), $db);
InsertRecord('chartmaster',array('accountcaode'),array('5765'),array('accountcode','accountname','group_'),array('5765','R','D'), $db);
InsertRecord('chartmaster',array('accountcaode'),array('5780'),array('accountcode','accountname','group_'),array('5780','T','D'), $db);
InsertRecord('chartmaster',array('accountcaode'),array('5785'),array('accountcode','accountname','group_'),array('5785','Voyages et loisirs','D'), $db);
InsertRecord('chartmaster',array('accountcaode'),array('5790'),array('accountcode','accountname','group_'),array('5790','Services publics','D'), $db);
InsertRecord('chartmaster',array('accountcaode'),array('5800'),array('accountcode','accountname','group_'),array('5800','Taxes d\'affaires, droits d\'adh','D'), $db);
InsertRecord('chartmaster',array('accountcaode'),array('5810'),array('accountcode','accountname','group_'),array('5810','Perte sur change','D'), $db);
?>
<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$LOCAL_LANG = Array(
	'default' => Array(
		'tx_powermail_title.description' => 'Insert a title for your form. Won\'t be displayed in Frontend.',
		'tx_powermail_html.description' => 'Should the emails be sent in HTML-Format?',
		'tx_powermail_multiple.description' => 'Make a multiple mailform.',
		'tx_powermail_multiple.details' => 'You can choose between these values:
		<b>Single Form</b>
		All Fields are displayed in one form on one page. No pagebreaks, no multiple steps.
		<b>Multiple JS</b>
		The form is divided into several steps. Each fieldset will create one step. The submitting is done by JavaScript. This provides a better performance.
		<b>Multiple PHP</b>
		The form is divided into several steps. Each fieldset will create one step. The submitting is done by PHP-script. This is better for accessibility.',
		'tx_powermail_fieldsets.description' => 'Create a new set of fields',
		'tx_powermail_fieldsets.details' => 'Formfields are kept together in individual fieldsets. This is for two reasons: 
		1. accessibility for disabled persons 
		2. for technical reasons. With fieldsets powermail is able to create a multiple form.',
		'tx_powermail_subject_r.description' => 'Email-subject of the recipient\'s mail',
		'tx_powermail_recipient.description' => 'Recipients of this mailform. To send to multiple recipients, separate with semicolon.',
		'tx_powermail_recip_table.description' => 'Choose a table to send this mailform to stores addresses.',
		'tx_powermail_recip_table.details' => 'With this field it is possible to send a massmail to a group of stores data. (e.g. fe_users). Just select a table.
		A new field will appear with further options to select.',
		'tx_powermail_recip_id.description' => 'Select either groups or different addresses as recipients.',
		'tx_powermail_recip_id.details' => 'If the table selected in previous field contains the string "group", you will find only groups inside this selectbox.
		Otherwise only individual email-Addresses are displayed.',
		'tx_powermail_query.description' => 'For admins: create an SQL-query to get very individual list of recipients.',
		'tx_powermail_query.details' => 'You can put any SQL-query inside here. Only limitation: the result of the query has to be a list of email-addresses.',
		'tx_powermail_sender.description' => 'Select a field in your form containing the sender\'s email-address. The answer-mail will be sent to this address.',
		'tx_powermail_subject_s.description' => 'Email-subject of the sender\'s mail',
		'tx_powermail_thanks.description' => 'Text that is displayed after submitting the form. For using markers see DETAILS',
		'tx_powermail_thanks.details' => 'You are able to set markers inside this text. Markers are set very easily - just use the field\'s name as wrapped with ###
		<b>Example</b>
		You have a field named "phone". So type the marker ###phone### into this field and the submitted content will be placed here.',
	),
	'de' => Array(
		'tx_powermail_title.description' => 'Geben Sie einen Titel für Ihr Formular ein. Dieser wird im Frontend nicht angezeigt.',
		'tx_powermail_html.description' => 'Sollen die E-Mails im HTML-Format gesendet werden?',
		'tx_powermail_multiple.description' => 'Formular in mehreren Schritten erstellen.',
		'tx_powermail_multiple.details' => 'Ist diese Option angewählt, wird das Formular in mehrere Schritte aufgeteilt. Jede Formularseite erstellt dann eine einzelne Seite.',
		'tx_powermail_fieldsets.description' => 'Create a new set of fields',
		'tx_powermail_fieldsets.details' => 'Formfields are kept together in individual fieldsets. This is for two reasons: 
		1. accessibility for disabled persons 
		2. for technical reasons. With fieldsets powermail is able to create a multiple form.',
		'tx_powermail_subject_r.description' => 'Email-subject of the recipient\'s mail',
		'tx_powermail_recipient.description' => 'Recipients of this mailform. To send to multiple recipients, separate with semicolon.',
		'tx_powermail_recip_table.description' => 'Choose a table to send this mailform to stores addresses.',
		'tx_powermail_recip_table.details' => 'With this field it is possible to send a massmail to a group of stores data. (e.g. fe_users). Just select a table.
		A new field will appear with further options to select.',
		'tx_powermail_recip_id.description' => 'Select either groups or different addresses as recipients.',
		'tx_powermail_recip_id.details' => 'If the table selected in previous field contains the string "group", you will find only groups inside this selectbox.
		Otherwise only individual email-Addresses are displayed.',
		'tx_powermail_query.description' => 'For admins: create an SQL-query to get very individual list of recipients.',
		'tx_powermail_query.details' => 'You can put any SQL-query inside here. Only limitation: the result of the query has to be a list of email-addresses.',
		'tx_powermail_sender.description' => 'Select a field in your form containing the sender\'s email-address. The answer-mail will be sent to this address.',
		'tx_powermail_subject_s.description' => 'Email-subject of the sender\'s mail',
		'tx_powermail_thanks.description' => 'Text that is displayed after submitting the form. For using markers see DETAILS',
		'tx_powermail_thanks.details' => 'You are able to set markers inside this text. Markers are set very easily - just use the field\'s name as wrapped with ###
		<b>Example</b>
		You have a field named "phone". So type the marker ###phone### into this field and the submitted content will be placed here.',
	),
	'dk' => Array(
		'tx_powermail_title.description' => 'Indsæt en titel til din formular. Vil ikke blive vist i Frontend.',
		'tx_powermail_html.description' => 'Skal email sendes i HTML-format?',
		'tx_powermail_multiple.description' => 'Opret en flersides mail formular.',
		'tx_powermail_multiple.details' => 'Du kan vælge mellem følgende:
		<b>Enkelt side formular</b>
		Alle felter vises på samme side. Ingen sideskift, ingen flere-trins mulighed.
		<b>Flersiders JS</b>
		Formularen deles op i flere trin. Hvert felt sæt vil oprette et trin. Afsendelsen sker med JavaScript. Dette giver bedre performance.
		<b>Flersiders PHP</b>
		Formularen deles op i flere trin. Hvert felt sæt vil oprette et trin. Afsendelsen sker med PHP-script. Dette giver bedre tilgængelighed.',
		'tx_powermail_fieldsets.description' => 'Opret et nyt felt sæt',
		'tx_powermail_fieldsets.details' => 'Formular felter holdes sammen i individuelle felt sæt. Det sker af to grunde: 
		1. Tilgængelighed for handicappede 
		2. Af tekniske grunde. Med felt sæt er Powermail i stand til at oprette en flersides formular.',
		'tx_powermail_subject_r.description' => 'Email overskift modtagerens mail',
		'tx_powermail_recipient.description' => 'Modtagere til denne formular. For at sende til flere modtagere, adskil modtageres adresser med semikolon.',
		'tx_powermail_recip_table.description' => 'Vælg en tabel til at gemme felter i.',
		'tx_powermail_recip_table.details' => 'Med dette felt er det muligt at sende en masse mail til en gruppe af modtagere. (Eks. fe_users). Vælg en tabel.
		Et nyt felt åbner med yderligere egenskaber.',
		'tx_powermail_recip_id.description' => 'Vælg enten modtager grupper eller forskellige adresser.',
		'tx_powermail_recip_id.details' => 'Hvis tabellen i det forrige indeholder ordet "group", så finder du kun grupper i det følgende.
		Ellers vil kun enkelte email adresser blive vist.',
		'tx_powermail_query.description' => 'For administratorer: Opret en SQL-forespørgsel for at hente en særligt oprettet liste af modtagere.',
		'tx_powermail_query.details' => 'Du kan sætte enhver type SQL heri. Den eneste begrænsning er: Resultatet af forspørgslen skal være en liste af email adresser.',
		'tx_powermail_sender.description' => 'Vælg et felt i din formular, som ineholder afsenders email adresse. Svar-mailen vil blive sendt til denne adresse.',
		'tx_powermail_subject_s.description' => 'Overskrift i afsenders email.',
		'tx_powermail_thanks.description' => 'Tekst, som vises efter afsendelse af formularen. For at bruge markers i teksten, se teksten DETAILS',
		'tx_powermail_thanks.details' => 'Du kan sætte markers i denne tekst. Markers sættes netm - brug blot feltets navn inde i ###
		<b>Eksempel</b>
		Du har feltet "telefon". Indtast ###telefon### i feltet her og i det fremsendte vil indholdet bliver erstattet af dette felt.',
	),
	'en' => Array(
		'tx_powermail_title.description' => 'Insert a title for your form. Won\'t be displayed in Frontend.',
		'tx_powermail_html.description' => 'Should the emails be sent in HTML-Format?',
		'tx_powermail_multiple.description' => 'Make a multiple mailform.',
		'tx_powermail_multiple.details' => 'You can choose between these values:
		<b>Single Form</b>
		All Fields are displayed in one form on one page. No pagebreaks, no multiple steps.
		<b>Multiple JS</b>
		The form is divided into several steps. Each fieldset will create one step. The submitting is done by JavaScript. This provides a better performance.
		<b>Multiple PHP</b>
		The form is divided into several steps. Each fieldset will create one step. The submitting is done by PHP-script. This is better for accessibility.',
		'tx_powermail_fieldsets.description' => 'Create a new set of fields',
		'tx_powermail_fieldsets.details' => 'Formfields are kept together in individual fieldsets. This is for two reasons: 
		1. accessibility for disabled persons 
		2. for technical reasons. With fieldsets powermail is able to create a multiple form.',
		'tx_powermail_subject_r.description' => 'Email-subject of the recipient\'s mail',
		'tx_powermail_recipient.description' => 'Recipients of this mailform. To send to multiple recipients, separate with semicolon.',
		'tx_powermail_recip_table.description' => 'Choose a table to send this mailform to stores addresses.',
		'tx_powermail_recip_table.details' => 'With this field it is possible to send a massmail to a group of stores data. (e.g. fe_users). Just select a table.
		A new field will appear with further options to select.',
		'tx_powermail_recip_id.description' => 'Select either groups or different addresses as recipients.',
		'tx_powermail_recip_id.details' => 'If the table selected in previous field contains the string "group", you will find only groups inside this selectbox.
		Otherwise only individual email-Addresses are displayed.',
		'tx_powermail_query.description' => 'For admins: create an SQL-query to get very individual list of recipients.',
		'tx_powermail_query.details' => 'You can put any SQL-query inside here. Only limitation: the result of the query has to be a list of email-addresses.',
		'tx_powermail_sender.description' => 'Select a field in your form containing the sender\'s email-address. The answer-mail will be sent to this address.',
		'tx_powermail_subject_s.description' => 'Email-subject of the sender\'s mail',
		'tx_powermail_thanks.description' => 'Text that is displayed after submitting the form. For using markers see DETAILS',
		'tx_powermail_thanks.details' => 'You are able to set markers inside this text. Markers are set very easily - just use the field\'s name as wrapped with ###
		<b>Example</b>
		You have a field named "phone". So type the marker ###phone### into this field and the submitted content will be placed here.',
	),	
	'nl' => Array(
		'tx_powermail_title.description' => 'Vul een titel in voor uw formulier. Dit zal niet worden getoond in de FrontEnd.',
		'tx_powermail_html.description' => 'Moet de email in HTML-Format worden verzonden?',
		'tx_powermail_multiple.description' => 'Maak een meerstappen mailform.',
		'tx_powermail_multiple.details' => 'U kunt kiezen uit de volgende waarden:
		<b>Enkel Formulier</b>
		Alle velden worden getoond in een formulier op een pagina. Geen pagina scheidingen, geen meerdere stappen.
		<b>Meervoudig JS</b>
		Het formulier is opgedeeld in meerdere stappen. Elke filedset zal een nieuwe stap maken. Het verzenden gebeurt via JavaScript. Dit is beter voor de performance.
		<b>Meervoudig PHP</b>
		Het formulier is opgedeeld in meerdere stappen. Elke filedset zal een nieuwe stap maken. Het verzenden gebeurt via JavaScript. Dit is beter voor de toegankelijkheid.',
		'tx_powermail_fieldsets.description' => 'Creeer een nieuwe serie velden',
		'tx_powermail_fieldsets.details' => 'Formuliervelden worden samengenomen in individuele filedsets. Dit is om twee redenen: 
		1. toegankelijkheid voor gehandicapten 
		2. voor technische redenen. Door middel van fieldsets is powermail in staat een meer stappen formulier te maken.',
		'tx_powermail_subject_r.description' => 'Onderwerp van de email aan de ontvanger.',
		'tx_powermail_recipient.description' => 'Ontvangers van dit formulier. Om te verzenden aan meer ontvangers plaats dan een punt-komma als scheidingsteken.',
		'tx_powermail_recip_table.description' => 'Kies een tabel waar dit formulier adressen in op kan slaan.',
		'tx_powermail_recip_table.details' => 'Met dit veld is het mogelijk een blukmail te versturen aan opgeslagen informatie (bijv. fe_users). Selecteer een tabel.
		Een nieuw veld zal verschijen met meer keuzemogelijkheden.',
		'tx_powermail_recip_id.description' => 'Selecteer groepen of meerdere adressen als ontavngers.',
		'tx_powermail_recip_id.details' => 'Als de tabel de string "group" bevat, vindt u alleen groepen in deze selector.
		Anders zullen alleen individuele email adressen worden getoond.',
		'tx_powermail_query.description' => 'Voor admins: creeer een SQL-query om een individuele lijst van ontvangers te krijgen.',
		'tx_powermail_query.details' => 'U kunt elke SQL-query hier invoeren. De enige beperking: het resultaat van de query moet een lijst van emailadressen zijn.',
		'tx_powermail_sender.description' => 'Selecteen een veld in uw formulier dat het email adres van de verzender bevat. De bevestigingsmail zal naar dit adres worden verzonden.',
		'tx_powermail_subject_s.description' => 'Onderwerp van de email van de verzender',
		'tx_powermail_thanks.description' => 'Tekst die wordt getoond na het verzenden van het formulier. Voor het gebruik van markers zie DETAILS',
		'tx_powermail_thanks.details' => 'U kunt markers in deze tekst zetten. Markers zijn makkelijk te plaatsen - gebruik de veldnamen ge-wrapped met ###
		<b>Voorbeeld</b>
		U hebt een veld met de naam "telefoon". Type de marker ###telefoon### in dit veld en de verzonden content zal hier geplaatst worden.',
	),
);
?>
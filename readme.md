Hier kunt u ons project zien, en ook openen.
Het maakt gebruik van PHP en dus is er een speciale server nodig om dit uit te voeren.
Mocht u dit niet hebben kunt u ook gebruik maken van onze website: https://janc-au-su.onrender.com/
Zelf hebben we MAMP gebruikt om een development server online te krijgen.
Ook is er voor het goed laten werken van de website een database nodig. De structuur hiervan kunt u nalezen in het bestand db_structure.sql.
Wegens veiligheidsoverwegingen hebben we de 'sleutel' van onze database beveiligd door een .env bestand te hebben gebruikt. Na het downloaden moet u die zelf nog aanmaken.
De sleutel is bijgevoegd bij de opdracht omdat de sleutel niet gepubliceerd kan worden op Github.
Ook moet u controleren of er daadwerkelijk een ca.pem bestand aanwezig is in de map. Zonder beide bestanden kan er geen verbinding worden gemaakt met onze website.
Ook zijn er twee bestanden in onze map (Dockerfile en entrypoint.sh) die doet niks voor de website als u hem zelf host, alleen zorgen ze ervoor dat de websit goed werkt op Render.
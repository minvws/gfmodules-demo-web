@extends('layouts.guest')

@section('content')
    <section>
        <div>
            <h1>Testidentiteiten en inlogmiddelen</h1>
            <p>Omdat de proeftuin een testapplicatie is kunt u inloggen met een testidentiteit. De gekoppelde inlogmiddelen zijn alleen met testaccounts te gebruiken. Testaccounts (of UZI-testpassen) worden niet actief verstrekt aan derde partijen. Deze pagina bevat meer informatie over de testidentiteiten en de implementatie van de diverse inlogmiddelen op de proeftuin.</p>

            <h2>Testidentiteiten</h2>
            <p>Binnen de proeftuin wordt gebruik gemaakt van testidentiteiten om in te loggen via het vernieuwde Dezi-stelsel. De testidentiteiten zijn volledig fictief en zo opgesteld dat deze verschillende scenario's van arbeidsrelaties vertegenwoordigen. Na het inloggen in De Ziekenboeg—het fictieve zorgplatform van de proeftuin—is te zien welke gegevens uit het UZI-register zijn opgehaald.</p>

            <h3>Beschikbare testidentiteiten</h3>

            <ul class="accordion">
                <li>
                    <button id="test-identity-1" aria-expanded="false"><strong>1 René van Laar:</strong> Cardioloog bij De Ziekenboeg.</button>
                    <div aria-labelledby="test-identity-1">
                        <h4>Inloggegevens</h4>
                        <dl>
                            <div>
                                <dt>Naam</dt>
                                <dd>René van Laar</dd>
                            </div>

                            <div>
                                <dt>Burgerservicenummer</dt>
                                <dd>958310828</dd>
                            </div>

                            <div>
                                <dt>UZI-nummer</dt>
                                <dd>999991772</dd>
                            </div>
                        </dl>

                        <h4>Relatie 1</h4>
                        <dl>
                            <div>
                                <dt>URA</dt>
                                <dd>42424242</dd>
                            </div>

                            <div>
                                <dt>Entiteit</dt>
                                <dd>De Ziekenboeg</dd>
                            </div>

                            <div>
                                <dt>Rolcode(s)</dt>
                                <dd>01.010 (Cardioloog)</dd>
                            </div>
                        </dl>
                    </div>
                </li>
                <li>
                    <button id="test-identity-2" aria-expanded="false"><strong>2 Keira Smith:</strong> Klinisch geriater, Reumatoloog en Specialist ouderengeneeskunde bij De Ziekenboeg.</button>
                    <div aria-labelledby="test-identity-2">
                        <h4>Inloggegevens</h4>
                        <dl>
                            <div>
                                <dt>Naam</dt>
                                <dd>Keira Smith</dd>
                            </div>

                            <div>
                                <dt>Burgerservicenummer</dt>
                                <dd>932763133</dd>
                            </div>

                            <div>
                                <dt>UZI-nummer</dt>
                                <dd>900020108</dd>
                            </div>
                        </dl>

                        <h4>Relatie 1</h4>
                        <dl>
                            <div>
                                <dt>URA</dt>
                                <dd>42424242</dd>
                            </div>

                            <div>
                                <dt>Entiteit</dt>
                                <dd>De Ziekenboeg</dd>
                            </div>

                            <div>
                                <dt>Rolcode(s)</dt>
                                <dd>01.022 (Klinisch geriater),<br>
                                    01.041 (Reumatoloog),<br>
                                    01.047 (Specialist ouderengeneeskunde)</dd>
                            </div>
                        </dl>
                    </div>
                </li>
                <li>
                    <button id="test-identity-3" aria-expanded="false"><strong>3 Anna Houtbeek:</strong> Tandarts bij De Ziekenboeg. En tandarts en orthodontist bij Tandartspraktijk Houtbeek.</button>
                    <div aria-labelledby="test-identity-3">
                        <h4>Inloggegevens</h4>
                        <dl>
                            <div>
                                <dt>Naam</dt>
                                <dd>Anna Houtbeek</dd>
                            </div>

                            <div>
                                <dt>Burgerservicenummer</dt>
                                <dd>989539817</dd>
                            </div>

                            <div>
                                <dt>UZI-nummer</dt>
                                <dd>900129838</dd>
                            </div>
                        </dl>

                        <h4>Relaties</h4>
                        <dl>
                            <div>
                                <dt>URA</dt>
                                <dd>42424242</dd>
                            </div>

                            <div>
                                <dt>Entiteit</dt>
                                <dd>De Ziekenboeg</dd>
                            </div>

                            <div>
                                <dt>Rolcode(s)</dt>
                                <dd>02.000 (Tandarts)</dd>
                            </div>
                        </dl>

                        <dl>
                            <div>
                                <dt>URA</dt>
                                <dd>72649228</dd>
                            </div>

                            <div>
                                <dt>Entiteit</dt>
                                <dd>Tandartspraktijk Houtbeek</dd>
                            </div>

                            <div>
                                <dt>Rolcode(s)</dt>
                                <dd>02.000 (Tandarts),<br>
                                    02.053 (Orthodontist)</dd>
                            </div>
                        </dl>
                    </div>
                </li>
                <li>
                    <button id="test-identity-4" aria-expanded="false"><strong>4 Sander van der Meer:</strong> Mondhygiënist bij Tandartspraktijk Houtbeek.</button>
                    <div aria-labelledby="test-identity-4">
                        <h4>Inloggegevens</h4>
                        <dl>
                            <div>
                                <dt>Naam</dt>
                                <dd>Sander van der Meer</dd>
                            </div>

                            <div>
                                <dt>Burgerservicenummer</dt>
                                <dd>982823046</dd>
                            </div>

                            <div>
                                <dt>UZI-nummer</dt>
                                <dd>900112981</dd>
                            </div>
                        </dl>

                        <h4>Relaties</h4>
                        <dl>
                            <div>
                                <dt>URA</dt>
                                <dd>72649228</dd>
                            </div>

                            <div>
                                <dt>Entiteit</dt>
                                <dd>Tandartspraktijk Houtbeek</dd>
                            </div>

                            <div>
                                <dt>Rolcode(s)</dt>
                                <dd>92.000 (Mondhygiënist)</dd>
                            </div>
                        </dl>
                    </div>
                </li>
            </ul>

            <h2>Inlogmiddelen</h2>

            <p>De inlogmiddelen op de proeftuin werken alleen met testaccounts. Omdat de proeftuin een testomgeving is, kan het zijn dat de beschikbare inlogmiddelen niet werken. Daarnaast wordt de proeftuin ook gebruikt door leveranciers van inlogmiddelen die de integratie met Dezi-Online willen testen. In de situatie dat er geen bruikbare inlogmiddelen zijn, kan het inloggen altijd worden getest door in te loggen met een testidentiteit.</p>

            <h3>DigiD</h3>
            <p>Dezi-Online heeft een technisch werkende koppeling met DigiD als inlogmiddel. Op dit moment is ervoor gekozen om DigiD in de proeftuin uit te schakelen, omdat derden alleen gebruik kunnen maken van de productie-omgeving van DigiD. Dit werkt niet in combinatie met het UZI-register van de proeftuin. Op dit moment is er een DigiD Mock inlogmiddel, zodat het inloggen via DigiD wel nagebootst kan worden voor testdoeleinden.</p>

            <h3>UZI-pas</h3>
            <p>De proeftuin ondersteunt het inloggen met een UZI-testpas. Deze passen worden uitgegeven door het CIBG, maar worden niet actief verstrekt aan derden.</p>

            <h3>Yivi</h3>
            <p>De proeftuin ondersteunt het inloggen via de Yivi wallet app. Om Yivi te gebruiken als inlogmethode moet een Yivi kaartje worden geladen in de wallet app. Dat is nodig bij de eerste keer inloggen via Yivi.</p>
        </div>
    </section>
@endsection


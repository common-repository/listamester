=== Listamester ===
Contributors: listamester
Tags: e-mail marketing, newsletter
Requires at least: 4.0.1
Tested up to: 6.5.2
Requires PHP: 7.4.0
Stable tag: 2.3.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
 
A listamester.hu online marketing szolgáltatás hivatalos Wordpress bővítménye. Bővebb információ: listamester.hu
 
== Description ==
 
Hogyan kell használni?

Az "Űrlapok" menüpont alatt találod az összes, a ListaMester fiókodban az új űrlapvarázslóval létrehozott űrlapjaidat.

ID = Az űrlap száma	
Név: Az űrlap neve, ahogy elnevezted a ListaMester fiókodban, amikor létrehoztad	
Csoport: Annak a csoportnak a neve, amihez létrehoztad az űrlapot (Figyelem: Egy csoporthoz tartozhat több űrlap is!)	
Shortcode: Kód az űrlap beillesztéséhez a honlapodba

A beillesztés módja 4 féle lehet.

A javascript beillesztési mód egyetlen sor kódot illeszt be, ami a legtöbb esetben jól működik, és szabványos módon illeszti be az űrlapot. Előfordulhat viszont, hogy bizonyos sablonok erőszakos CSS szabályai felülírnak valamilyen formázási szabályokat az űrlapban, és picit változik a külalakja. Ez az ajánlott beillesztési mód.
A html beillesztési mód html kódot illeszt az oldalba, annak CSS szabályaival együtt. Ez nem teljesen szabványos, de jelenleg minden böngészőben működik. Akkor érdemes használni, ha ragaszkodsz ahhoz, hogy a kódok ott legyenek az oldalban, mert pl. picit módosítani is szeretnéd. Figyelem! Ez egy veszélyes módszer, mert a szövegszerkesztő hajlamos a kódokat megváltoztatni. Csak haladó felhasználóknak vagy profi webdizájnereknek ajánljuk ezt a beillesztési módot.
A legbiztonságosabb módszer egyértelműen az iframe, ami egy aloldalban tölti be az űrlapot. Ez bármilyen környezetben pontosan ábrázolja az űrlapot, és garantáltan jól működik, mert a ListaMester kódjait változatlan formában és üres CSS környezetben hozza be. Hátránya, hogy a köszönő oldal is benne marad az űrlap dobozában (nem lehet belőle kimenni). Emiatt sokan nem szeretik, de ezt ajánljuk, ha a javascript beillesztéssel gondok lennének.
Exit popup. Az űrlap akkor ugrik fel, amikor az olvasó elhagyja az oldalt. Javascript alapú beillesztéssel működik.
A kiválasztott űrlap beillesztéséhez másold ki az űrlap shortcode-ját a szögletes zárójelekkel együtt, majd másold be a honlapodon a tartalomba oda, ahova szeretnéd beilleszteni!
 
== Installation ==
 
Telepítés: az "Új bővítmény hozzáadása" menüponton keresztül a .zip fájl feltöltésével
 
 
== Frequently Asked Questions ==
 
= Hol kaphatok segítséget a bővítménnyel kapcsolatban? =
 
Bővebb információk itt: https://www.listamester.hu/utmutato-listamesterhez/urlapok/feliratkozasi-urlap-beillesztese-wordpress-honlapba

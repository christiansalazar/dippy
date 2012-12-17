Dippy Widget
============

by:

Christian Salazar. christiansalazarh@gmail.com	@yiienespanol, dic. 2012.

![Screen Capture]
(https://bitbucket.org/christiansalazarh/dippy/downloads/screenshot.png "Screen Capture")

[http://opensource.org/licenses/bsd-license.php](http://opensource.org/licenses/bsd-license.php "http://opensource.org/licenses/bsd-license.php")

[Repository at Bit Bucket !](https://bitbucket.org/christiansalazarh/dippy/ 
 "Repository at Bit Bucket !")

#Requirement: 

Yii  1.1.12


#What it does ?

[EN]
This widget helps you when dependent objects are required on your model,
making it easy for you (as user) to create master-detail items.  
Every "Dippy" widget can work togheter with another 'Dippy' widget on the same 
view, in cascade, the second Dippy widget listen for changes in the first 
Dippy widget, when a change occurs in the first widget a 'refresh' event is 
fired for the second 'Dippy' Widget.

Example use case:

Suppose you have an article, it needs some attributes specified by the user, so
you can use a Dippy widget #1 in wich the user creates: "Flavor", "Package" 
items, and so on. 
As you know, many flavors exists, your user wants to insert each
flavor and each package, to achive this task you can insert a new Dippy 
Widget #2 in the same view.
in the second Dippy Widget your user inserts the Flavors..Peach, Orange, each
one associated with a parent: The seleceted Flavor in Dippy Widget #1. When
your user select another item in Dippy Widget #1 the widget #2 automatically
refresh and show associated content.

[ES]
Este widget te ayuda cuando necesitas crear objetos dependientes en tu modelo,
haciendo que la creacion de objetos sea facil para el usuario en un escenario
maestro-detalle.
Cada widget 'Dippy' puede trabajar concatenado con otro Widget 'Dippy' en la
misma vista, en cascada, el segundo widget Dippy estara atento a los cambios
de seleccion del primero Widget causando un evento 'refresh' cuando hay
cambio de seleccion en el primer widget.

Caso de ejemplo:

Para un articulo que se vende en internet, necesitas indicar atributos,
por ejemplo: 'Sabor', 'Empaque'.  El primer Widget Dippy te permite crear
estos items, asociandolos a un Articulo (su maestro).

Un segundo widget Dippy, podrá usarse para crear los Sabores, y los Empaques,
dependiendo de si se selecciono 'Sabor' o 'Empaque' en el primer widget, 
cuando la seleccion cambia en el primer Widget el segundo se actualiza.  Al
crea un nuevo 'Sabor' este dato quedara enlazado con su maestro, por ejemplo,
'Peach', 'Orange' son sabores que el usuario va insertando.

#Usage

## Insert and configure the Widget.

~~~
[php]
~~~


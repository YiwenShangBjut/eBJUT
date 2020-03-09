import 'package:flutter/material.dart';

/**
 * colors
 */
Color themeColor=Colors.white;
Color bjutBlue=Color.fromRGBO(0, 130, 180, 1.0);

/**
 * app bar
 */
AppBar topBar=new AppBar( //top app bar style
  title: Text("EBJUT (Demo)"),
);

/**
 * bottom navigation bar item
 */
BottomNavigationBarItem BnbiHome=new BottomNavigationBarItem(icon: Icon(Icons.home), title: Text('Home'));
BottomNavigationBarItem BnbiMessage=new BottomNavigationBarItem(icon: Icon(Icons.crop_square), title: Text('Square'));
BottomNavigationBarItem BnbiTool=new BottomNavigationBarItem(icon: Icon(Icons.shopping_cart), title: Text('Toolbox'));
BottomNavigationBarItem BnbiMe=new BottomNavigationBarItem(icon: Icon(Icons.person), title: Text('Me'));

/**
 * buttons
 */
Container buttonMoments(){
  return Container(
    height: 160.0,
    child: FlatButton(
      onPressed:() {

      },
      child: Column(
        children: <Widget>[
          new SizedBox(height: 25.0),
          new Image.asset("images/moments.png", fit: BoxFit.cover, gaplessPlayback: true),
          new SizedBox(height: 10.0),
          new Text("Moments"),
        ],
      ),
    ),
  );
}

Container buttonLost(){
  return Container(
    height: 160.0,
    child: FlatButton(
      onPressed:() {

      },
      child: Column(
        children: <Widget>[
          new SizedBox(height: 25.0),
          new Image.asset("images/lost.png", fit: BoxFit.cover, gaplessPlayback: true),
          new SizedBox(height: 10.0),
          new Text("Lost&Found"),
        ],
      ),
    ),
  );
}

Container buttonSecond(){
  return Container(
    height: 160.0,
    child: FlatButton(
      onPressed:() {

      },
      child: Column(
        children: <Widget>[
          new SizedBox(height: 25.0),
          new Image.asset("images/second.png", fit: BoxFit.cover, gaplessPlayback: true),
          new SizedBox(height: 10.0),
          new Text("Second-hand"),
        ],
      ),
    ),
  );
}

Container buttonForum(){
  return Container(
    height: 160.0,
    child: FlatButton(
      onPressed:() {

      },
      child: Column(
        children: <Widget>[
          new SizedBox(height: 25.0),
          new Image.asset("images/forum.png", fit: BoxFit.cover, gaplessPlayback: true),
          new SizedBox(height: 10.0),
          new Text("Forum"),
        ],
      ),
    ),
  );
}

Container buttonPrinter(){
  return Container(
    height: 160.0,
    child: FlatButton(
      onPressed:() {

      },
      child: Column(
        children: <Widget>[
          new SizedBox(height: 25.0),
          new Image.asset("images/printer.png", fit: BoxFit.cover, gaplessPlayback: true),
          new SizedBox(height: 10.0),
          new Text("Printer"),
        ],
      ),
    ),
  );
}

Container buttonTimetable(){
  return Container(
    height: 160.0,
    child: FlatButton(
      onPressed:() {

      },
      child: Column(
        children: <Widget>[
          new SizedBox(height: 25.0),
          new Image.asset("images/timetable.png", fit: BoxFit.cover, gaplessPlayback: true),
          new SizedBox(height: 10.0),
          new Text("Timetable"),
        ],
      ),
    ),
  );
}

Container buttonPhonebook(){
  return Container(
    height: 160.0,
    child: FlatButton(
      onPressed:() {

      },
      child: Column(
        children: <Widget>[
          new SizedBox(height: 25.0),
          new Image.asset("images/phonebook.png", fit: BoxFit.cover, gaplessPlayback: true),
          new SizedBox(height: 10.0),
          new Text("Phonebook"),
        ],
      ),
    ),
  );
}

Container buttonCalendar(){
  return Container(
    height: 160.0,
    child: FlatButton(
      onPressed:() {

      },
      child: Column(
        children: <Widget>[
          new SizedBox(height: 25.0),
          new Image.asset("images/calendar.png", fit: BoxFit.cover, gaplessPlayback: true),
          new SizedBox(height: 10.0),
          new Text("Calendar"),
        ],
      ),
    ),
  );
}
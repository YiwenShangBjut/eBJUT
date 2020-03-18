/**
 * This page is to load appBar & bottomNav
 */
import 'package:flutter/material.dart';
import 'home-page.dart';
import 'style.dart';
import 'profile.dart';
import'./toolBox.dart';
import 'package:ebjut/square.dart';

class index extends StatefulWidget{
  @override
  _indexState createState()  => new _indexState();
}

class _indexState extends State<index> {
  int _currentIndex=0;

  final _pageList=[
    MyHomePage(),
    Square(),
    ToolBox(),
    Profile(),
  ];

  @override
  Widget build(BuildContext context) {

    BottomNavigationBar bottomNavBar = new  BottomNavigationBar( //bottom nav bar style
      type: BottomNavigationBarType.fixed, //if changed the fixedColor is invalid
      currentIndex: _currentIndex,
      onTap: onTabTapped,
      fixedColor: bjutBlue,
      items: [ BnbiHome, BnbiMessage, BnbiTool, BnbiMe ], //store in style.dart
    );

    return Scaffold(

//      appBar: topBar, //store in style.dart
      body: IndexedStack(
        index: _currentIndex,
        children: _pageList,
      ),//body are different pages
      bottomNavigationBar: bottomNavBar,
    );
  }

  void onTabTapped(int index) { //change state
    setState(() {
      _currentIndex = index;
    });
  }

}
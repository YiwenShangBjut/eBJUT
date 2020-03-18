/**
 * This page is to start app
 */
import 'dart:async';
import 'package:flutter/material.dart';
import 'index.dart';
import 'style.dart';
import 'dart:io';
import 'package:flutter/services.dart';

Future main() async {
  runApp(new App()); //main function: run app

  if (Platform.isAndroid) { //Transparent status bar
    SystemUiOverlayStyle systemUiOverlayStyle = SystemUiOverlayStyle( statusBarColor: Colors.transparent );
    SystemChrome.setSystemUIOverlayStyle(systemUiOverlayStyle);
  }

}

class App extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'eBJUT',
      theme: ThemeData(primaryColor: themeColor), //store in style.dart
      home: index(),
    );
  }
}
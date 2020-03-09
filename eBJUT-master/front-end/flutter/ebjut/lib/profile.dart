import 'package:flutter/material.dart';
import 'login.dart';

class Profile extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return new MaterialApp(
        home: Scaffold(
          body: new Login(),
        ));
  }
}

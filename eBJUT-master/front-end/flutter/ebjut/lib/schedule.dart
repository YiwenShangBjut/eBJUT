import 'package:flutter/material.dart';

class Schedule extends StatefulWidget {
  @override
  createState() => new _Schedule();
}
class _Schedule extends State<Schedule>{
  @override
  Widget build(BuildContext context) {
    return new Scaffold(
      appBar: new AppBar(
          title: new Text('curriculum schedule'),
      ),
      body: new Center(

        child: new Text('pageText2'),

      ),
    );
  }


}
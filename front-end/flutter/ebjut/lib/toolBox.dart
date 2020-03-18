import 'package:flutter/material.dart';
import 'style.dart';

class ToolBox extends StatefulWidget {
  @override
  _ToolBox createState() => _ToolBox();
}

class _ToolBox extends State<ToolBox> {

  Widget build(BuildContext context) {
//    return Scaffold(
//      body: new Center(
//        child: gv(),
//      ),
//    );

    return Scaffold(
      body: Form(
        child: ListView(
          padding: EdgeInsets.symmetric(horizontal: 22.0),
          children: <Widget>[
            SizedBox(
              height: kToolbarHeight,
            ),
            buildTitle(),
            gv(),
          ],
          shrinkWrap: true,
        )
      ),
    );

  }

  GridView gv(){
    return GridView(
      shrinkWrap:true,
      gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
        crossAxisCount: 3,
        childAspectRatio: 1.0,
      ),
      children: <Widget>[
        buttonMoments(),
        buttonLost(),
        buttonSecond(),
        buttonForum(),
        buttonCalendar(),
        buttonTimetable(),
        buttonPhonebook(),
        buttonPrinter(),
        buttonMoments(),
        buttonMoments(),
        buttonMoments(),
      ],
    );
  }

  Padding buildTitle() {
    return Padding(
      padding: EdgeInsets.all(8.0),
      child: Row(
        children: <Widget>[
          Text('ToolBox', style: TextStyle(fontSize: 42.0)),
        ],
      ),
    );
  }

}
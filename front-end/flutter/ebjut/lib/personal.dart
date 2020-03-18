import 'package:flutter/material.dart';

class Personal extends StatefulWidget {
  @override
  _Personal createState() => _Personal();
}

class _Personal extends State<Personal> {
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Form(
          child: ListView(
            children: <Widget>[
              userAccounts(),
              list1(),
              list1(),
              list1(),
              list1(),
              list1(),
              list1(),
            ],
          ),
      ),
    );
  }

  UserAccountsDrawerHeader userAccounts(){
    return UserAccountsDrawerHeader(
      accountName: new Text('UserName',style: TextStyle(fontSize: 20.0)),
      accountEmail: new Text('Student ID: '),
      currentAccountPicture: new GestureDetector(
        onTap: () => print('current user'),
        child: new CircleAvatar(
          backgroundImage: new NetworkImage('https://www.moerats.com/usr/themes/handsome/usr/img/sj2/6.jpg'),
        ),
      ),
      decoration: new BoxDecoration(
        image: new DecorationImage(
          fit: BoxFit.fill,
//           image: new NetworkImage('http://img.imharbin.com/wp-content/uploads/2019/03/2019-04-07-09-58-42-733292-imharbin.com.jpg?imageView2/1/w/420/h/260/q/100#')
          image: new ExactAssetImage('images/lake.jpg'),
        ),
      ),
    );
  }

  ListTile list1(){
    return ListTile(
      title: new Text('First Page'),
      trailing: new Icon(Icons.arrow_upward),
      onTap: () {
//        Navigator.push(context, new MaterialPageRoute(builder: (BuildContext context){
//          return new Login();
//        }));
      }
    );
  }

}
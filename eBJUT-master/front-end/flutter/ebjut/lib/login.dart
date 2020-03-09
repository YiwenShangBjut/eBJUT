import 'package:flutter/material.dart';
import 'register.dart';
import 'package:dio/dio.dart';
import 'dart:convert';
import 'dart:async';
import 'personal.dart';

class Login extends StatefulWidget {
  @override
  _Login createState() => _Login();
}

class _Login extends State<Login> {
  final _formKey = GlobalKey<FormState>();
  String sID, password;
  var msg,code;
  bool isObscure = true;
  Color _eyeColor;
  String loginText="Login";

  @override
  Widget build(BuildContext context) {
    return Scaffold(
        body: Form(
            key: _formKey,
            child: ListView(
              padding: EdgeInsets.symmetric(horizontal: 22.0),
              children: <Widget>[
                SizedBox(
                  height: kToolbarHeight,
                ),
                buildTitle(),
                SizedBox(height: 70.0),
                buildIDTextField(),
                SizedBox(height: 30.0),
                buildPasswordTextField(context),
                buildForgetPasswordText(context),
                SizedBox(height: 60.0),
                buildLoginButton(context),
                SizedBox(height: 30.0),
              ],
            )));
  }

  Align buildLoginButton(BuildContext context) {
    return Align(
      child: SizedBox(
        height: 45.0,
        width: 270.0,
        child: RaisedButton(
          child: Text(
            loginText,
            style: TextStyle(fontSize: 20.0, color: Colors.white),
          ),
          color: Colors.black,
          onPressed: () {
            if (_formKey.currentState.validate()) {
              _formKey.currentState.save();
              changeState();
              print('\n----- ID:$sID , password:$password -----\n');
              post();
              new Timer(Duration(milliseconds:1000), () => print("$msg,$code"));
              new Timer(Duration(milliseconds:2000), () => toLogin());
            }
          },
          shape: StadiumBorder(side: BorderSide()), //circular bead style
        ),
      ),
    );
  }

  Padding buildForgetPasswordText(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.only(top: 8.0),
      child: Align(
        alignment: Alignment.centerRight,
        child: FlatButton(
          child: Text(
            'Forget Password？',
            style: TextStyle(fontSize: 14.0, color: Colors.grey),
          ),
          onPressed: () {
//            Navigator.pop(context);
          },
        ),
      ),
    );
  }

  TextFormField buildPasswordTextField(BuildContext context) {
    return TextFormField(
      onSaved: (String value) => password = value,
      obscureText: isObscure,
      validator: (String value) {
        if (value.isEmpty) {
          return 'Please input a password';
        }
      },
      decoration: InputDecoration(
          labelText: 'Password',
          suffixIcon: IconButton(
              icon: Icon(
                Icons.remove_red_eye,
                color: _eyeColor,
              ),
              onPressed: () {
                setState(() {
                  isObscure = !isObscure;
                  _eyeColor = isObscure
                      ? Colors.grey
                      : Theme.of(context).iconTheme.color;
                });
              })),
    );
  }

  Padding buildTitle() {
    return Padding(
      padding: EdgeInsets.all(8.0),
      child: Row(
        children: <Widget>[
          Text('Login', style: TextStyle(fontSize: 42.0)),
          buildTitleLineV(),
          FlatButton(
            child: Text(
              'Register',
              style: TextStyle(fontSize: 20.0, color: Colors.grey),
            ),
            onPressed: () {//on press return new register interface
              Navigator.of(context).pushAndRemoveUntil(
                  new MaterialPageRoute(builder: (context) => new Register() ), (route) => route == null
              );
            },
          ),
        ],
      ),
    );
  }

  TextFormField buildIDTextField() {
    return TextFormField(
      decoration: InputDecoration(
        labelText: 'Student ID / Username / Phone',
      ),
      validator: (String value) {},
      onSaved: (String value) => sID = value,
    );
  }

  Padding buildTitleLineV() {
    return Padding(
      padding: EdgeInsets.only(left: 12.0, top: 4.0),
      child: Align(
        alignment: Alignment.center,
        child: Container(
          color: Colors.black,
          width: 2.0,
          height: 35.0,
        ),
      ),
    );
  }

  void post() async {
    Response response;
    Dio dio = new Dio();
    FormData formData = new FormData.from({
      "user_login": sID,
      "user_password": password,
    });
    response = await dio.post("http://47.88.225.51/api/v1/login.php", data: formData);
    print(response.data);
    Map<String, dynamic> message = json.decode(response.data);
    msg=message['msg'];
    code=message['code'];
  }

  void toLogin(){
    if(code==200){
      setState(() {
        loginText="Successful!";
      });
      new Timer(Duration(milliseconds:2000), () =>
          Navigator.of(context).pushAndRemoveUntil(
              new MaterialPageRoute(builder: (context) => new Personal() ), (route) => route == null
          )
      );
    }else if(code==401){
      setState(() {
        loginText="Not exists!";
      });
      new Timer(Duration(milliseconds:2000), () =>
          Navigator.of(context).pushAndRemoveUntil(
              new MaterialPageRoute(builder: (context) => new Login() ), (route) => route == null
          )
      );
    }
  }

  void changeState(){
    setState(() {
      loginText="waiting...";
    });
  }

}
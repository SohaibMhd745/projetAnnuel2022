package com.akm.ui;

import com.akm.back.*;
import org.json.JSONObject;
import javax.swing.*;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.util.HashMap;

public class ConnectionPage extends GUI{
    private JLabel coLabel;
    private JLabel mailLabel;
    private JLabel passLabel;
    private JLabel errLabel;
    private JTextField mailField;
    private JPasswordField passField;
    private JButton connect;

    public ConnectionPage(){
        super(1, 7);
        coLabel = new JLabel("Connection");
        mailLabel = new JLabel("Email");
        passLabel = new JLabel("Mot de passe");
        errLabel = new JLabel("");

        mailField = new JTextField(30);
        passField = new JPasswordField(30);

        connect = new JButton("Connexion");
        connect.addActionListener(new ActionListener() {
            @Override
            public void actionPerformed(ActionEvent e) {
                String mail = mailField.getText();
                String pass = String.valueOf(passField.getPassword());

                HashMap<String, String> params = new HashMap<String, String>();
                params.put("email", mail);
                params.put("password", pass);

                String body = JsonHandler.toJsonString(params);
                JSONObject response = AkmApi.requestApi(AkmApi.Actions.SIGN_IN, body);
                boolean success = response.getBoolean("success");
                if(success){
                    hide();
                    MainProcess.callUserPage(response.getString("token"));
                }else{
                    AkmException exception = AkmException.getExceptionFromCode(response.getInt("errorCode"));
                    errLabel.setText(exception.errorLabel);
                }
            }
        });

        addElem(0,0, coLabel);
        addElem(0,1, mailLabel);
        addElem(0,2, mailField);
        addElem(0,3, passLabel);
        addElem(0,4, passField);
        addElem(0,5, connect);
        addElem(0,6, errLabel);
    }
}
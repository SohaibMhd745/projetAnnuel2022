package com.akm.front;

import com.akm.back.*;
import org.json.JSONObject;
import java.awt.event.ActionListener;
import java.util.HashMap;
import javafx.fxml.FXMLLoader;
import javafx.scene.Node;
import javafx.scene.Scene;
import javafx.scene.image.Image;
import javafx.stage.Stage;
import java.util.Objects;
import javax.swing.JButton;
import javax.swing.JPasswordField;
import javax.swing.JTextField;

public class AKMController {
    private final JTextField mailField = new JTextField(30);
    private final JPasswordField passField = new JPasswordField(30);
    private final JButton connect = new JButton("Connexion");

    public void switchLogin(javafx.event.ActionEvent event) {
        FXMLLoader fxmlLoader = new FXMLLoader(getClass().getResource("login.fxml"));
        Stage stage = (Stage)((Node)event.getSource()).getScene().getWindow();
        Scene scene = new Scene(fxmlLoader.load(), 1280, 720);
        Image icon = new Image(Objects.requireNonNull(AKMController.class.getResourceAsStream("/assets/logo.png")));
        stage.getIcons().add(icon);
        stage.setTitle("AKM Gestion - Connexion");
        stage.setScene(scene);
        stage.show();
    }

    public void attemptLogin(javafx.event.ActionEvent event) {
        this.connect.addActionListener(new ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent e) {
                String mail = AKMController.this.mailField.getText();
                String pass = String.valueOf(AKMController.this.passField.getPassword());
                HashMap<String, String> params = new HashMap();
                params.put("email", mail);
                params.put("password", pass);
                String body = JsonHandler.toJsonString(params);
                JSONObject response = AkmApi.requestApi(AkmApi.Actions.SIGN_IN, body);
                boolean success = response.getBoolean("success");
                if (success) {
                    AKMController.this.hide();
                    MainProcess.callUserPage(response.getString("token"));
                } else {
                    AkmException exception = AkmException.getExceptionFromCode(response.getInt("errorCode"));
                    AKMController.this.errLabel.setText(exception.errorLabel);
                }

            }
        });
        this.addElem(0, 0, this.coLabel);
        this.addElem(0, 1, this.mailLabel);
        this.addElem(0, 2, this.mailField);
        this.addElem(0, 3, this.passLabel);
        this.addElem(0, 4, this.passField);
        this.addElem(0, 5, this.connect);
        this.addElem(0, 6, this.errLabel);
    }
}
package com.akm.front;

import com.akm.back.AkmApi;
import com.akm.back.AkmException;
import com.akm.back.JsonHandler;
import javafx.fxml.FXMLLoader;
import javafx.scene.Node;
import javafx.scene.Scene;
import javafx.scene.image.Image;
import javafx.stage.Stage;
import org.json.JSONObject;

import javax.swing.*;
import java.awt.event.ActionListener;
import java.io.IOException;
import java.util.HashMap;
import java.util.Objects;

public class AKMController {
    private final JTextField mailField = new JTextField(30);
    private final JPasswordField passField = new JPasswordField(30);
    private final JButton connect = new JButton("Connexion");

    public void switchLogin(javafx.event.ActionEvent event) throws IOException {
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
                    System.out.println("réussi");
                } else {
                    AkmException exception = AkmException.getExceptionFromCode(response.getInt("errorCode"));
                    System.out.println("échec");
                }

            }
        });
    }
}
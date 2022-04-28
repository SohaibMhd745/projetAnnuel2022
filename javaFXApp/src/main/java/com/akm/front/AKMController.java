package com.akm.front;

import com.akm.back.AkmApi;
import com.akm.back.AkmException;
import com.akm.back.JsonHandler;
import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.scene.Node;
import javafx.scene.Scene;
import javafx.scene.control.PasswordField;
import javafx.scene.control.TextField;
import javafx.scene.image.Image;
import javafx.stage.Stage;
import org.json.JSONObject;

import java.io.IOException;
import java.util.HashMap;
import java.util.Objects;

public class AKMController {
    @FXML
    private static TextField email;

    @FXML
    private static PasswordField password;

    public void switchLogin(javafx.event.ActionEvent event) throws IOException {
        FXMLLoader fxmlLoader = new FXMLLoader(getClass().getResource("login.fxml"));
        Stage stage = (Stage)((Node)event.getSource()).getScene().getWindow();
        Scene scene = new Scene(fxmlLoader.load(), 1280, 720);
        Image icon = new Image(Objects.requireNonNull(AKMController.class.getResourceAsStream("/assets/logo.png")));
        stage.getIcons().add(icon);
        stage.setTitle("AKM Gestion - Connexion");
        stage.setScene(scene);
        stage.show();
        email = (TextField) scene.lookup("#email");
        password = (PasswordField) scene.lookup("#password");
    }

    public void attemptLogin() {
        String emailValue = AKMController.email.getText();
        String passwordValue = AKMController.password.getText();
        HashMap<String, String> params = new HashMap();
        params.put("email", emailValue);
        params.put("password", passwordValue);
        String body = JsonHandler.toJsonString(params);
        JSONObject response = AkmApi.requestApi(AkmApi.Actions.SIGN_IN, body);
        boolean success = response.getBoolean("success");
        if (success) {
            System.out.println(response.toString());
        } else {
            AkmException exception = AkmException.getExceptionFromCode(response.getInt("errorCode"));
            System.out.println(response.toString());
            System.out.println(exception.errorLabel);
        }
    }
}
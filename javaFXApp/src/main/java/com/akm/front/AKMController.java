package com.akm.front;

import com.akm.back.AkmApi;
import com.akm.back.AkmException;
import com.akm.back.JsonHandler;
import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.scene.Node;
import javafx.scene.Scene;
import javafx.scene.control.CheckBox;
import javafx.scene.control.Label;
import javafx.scene.control.PasswordField;
import javafx.scene.control.TextField;
import javafx.scene.image.Image;
import javafx.stage.Stage;
import org.json.JSONObject;
import java.io.*;
import java.util.HashMap;
import java.util.Objects;
import java.util.Scanner;

public class AKMController {
    @FXML
    private static TextField loginEmail;
    @FXML
    private static PasswordField loginPassword;
    @FXML
    private static Label loginError;
    @FXML
    private static CheckBox loginCheckbox;

    /**
     * Sends login request to API
     */
    public void attemptLogin() throws IOException {
        String emailValue = AKMController.loginEmail.getText();
        String passwordValue = AKMController.loginPassword.getText();
        HashMap<String, String> params = new HashMap<String, String>();
        params.put("email", emailValue);
        params.put("password", passwordValue);
        String body = JsonHandler.toJsonString(params);
        JSONObject response = AkmApi.requestApi(AkmApi.Actions.SIGN_IN, body);
        boolean success = response.getBoolean("success");
        if (success) {
            doStayLogged(response);
            switchHome();
        } else {
            AkmException exception = AkmException.getExceptionFromCode(response.getInt("errorCode"));
            loginError.setText(exception.errorLabel + ".");
        }
    }

    /* Switch window functions */

    /**
     * Shows login page
     * @param event Button used to call function
     */
    public void switchLogin(javafx.event.ActionEvent event) throws IOException {
        if (askForLogin()) {
            FXMLLoader fxmlLoader = new FXMLLoader(getClass().getResource("login.fxml"));
            Stage stage = (Stage)((Node)event.getSource()).getScene().getWindow();
            Scene scene = new Scene(fxmlLoader.load(), 1280, 720);
            Image icon = new Image(Objects.requireNonNull(AKMController.class.getResourceAsStream("/assets/logo.png")));
            stage.getIcons().add(icon);
            stage.setTitle("AKM Gestion - Connexion");
            stage.setScene(scene);
            stage.show();
            loginEmail = (TextField) scene.lookup("#loginEmail");
            loginPassword = (PasswordField) scene.lookup("#loginPassword");
            loginError = (Label) scene.lookup("#loginError");
            loginCheckbox = (CheckBox) scene.lookup("#loginCheckbox");
        } else switchHome();
    }

    /**
     * Shows home page
     */
    public void switchHome() throws IOException {
        FXMLLoader fxmlLoader = new FXMLLoader(getClass().getResource("home.fxml"));
        Stage stage = new Stage();
        Scene scene = new Scene(fxmlLoader.load(), 1280, 720);
        Image icon = new Image(Objects.requireNonNull(AKMController.class.getResourceAsStream("/assets/logo.png")));
        stage.getIcons().add(icon);
        stage.setTitle("AKM Gestion - Accueil");
        stage.setScene(scene);
        stage.show();
    }

    public void switchPrestations(javafx.event.ActionEvent event) throws IOException {
        FXMLLoader fxmlLoader = new FXMLLoader(getClass().getResource("prestations.fxml"));
        Stage stage = (Stage)((Node)event.getSource()).getScene().getWindow();
        Scene scene = new Scene(fxmlLoader.load(), 1280, 720);
        Image icon = new Image(Objects.requireNonNull(AKMController.class.getResourceAsStream("/assets/logo.png")));
        stage.getIcons().add(icon);
        stage.setTitle("AKM Gestion - Prestations");
        stage.setScene(scene);
        stage.show();
    }

    public void switchConversion(javafx.event.ActionEvent event) throws IOException {
        FXMLLoader fxmlLoader = new FXMLLoader(getClass().getResource("conversion.fxml"));
        Stage stage = (Stage)((Node)event.getSource()).getScene().getWindow();
        Scene scene = new Scene(fxmlLoader.load(), 1280, 720);
        Image icon = new Image(Objects.requireNonNull(AKMController.class.getResourceAsStream("/assets/logo.png")));
        stage.getIcons().add(icon);
        stage.setTitle("AKM Gestion - Conversion");
        stage.setScene(scene);
        stage.show();
    }

    /* Utility functions */

    /**
     * Writes token/null in .txt file depending on checkbox value
     * @param response Returned by API
     */
    private void doStayLogged(JSONObject response) throws IOException {
        BufferedWriter writer = new BufferedWriter(new FileWriter("stayLogged.txt", false));
        if(loginCheckbox.isSelected()) writer.write(response.getString("token"));
        else writer.write("null");
        writer.close();
    }

    /**
     * Reads token/null in .txt file
     * @return Boolean if token is set in file
     */
    private boolean askForLogin() throws FileNotFoundException {
        File file = new File("stayLogged.txt");
        Scanner scanner = new Scanner(file);
        String data = scanner.nextLine();
        return Objects.equals(data, "null");
    }
}
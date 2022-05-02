package com.akm.front;

import com.akm.back.AkmApi;
import com.akm.back.AkmException;
import com.akm.back.JsonHandler;
import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.geometry.Insets;
import javafx.scene.Node;
import javafx.scene.Scene;
import javafx.scene.control.Label;
import javafx.scene.control.PasswordField;
import javafx.scene.control.TextField;
import javafx.scene.image.Image;
import javafx.scene.layout.Background;
import javafx.scene.layout.BackgroundFill;
import javafx.scene.layout.CornerRadii;
import javafx.scene.layout.GridPane;
import javafx.scene.paint.Color;
import javafx.stage.Stage;
import org.json.JSONArray;
import org.json.JSONObject;
import java.io.IOException;
import java.util.HashMap;
import java.util.Objects;

public class AKMController {
    @FXML
    private static TextField loginEmail;
    @FXML
    private static PasswordField loginPassword;
    @FXML
    private static Label loginError;

    private static String partnerID;
    private String getPartnerID() {
        return partnerID;
    }
    private void setPartnerID(String partnerID) {
        AKMController.partnerID = partnerID;
    }

    /* Switch window functions */

    /**
     * Shows login page
     * @param event Button used to call function
     */
    public void switchLogin(javafx.event.ActionEvent event) throws IOException {
        /*
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
        */
        attemptLoginEmailPass();
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

    /**
     * Shows prestations page
     * @param event Button used to call function
     */
    public void switchPrestations(javafx.event.ActionEvent event) throws IOException {
        FXMLLoader fxmlLoader = new FXMLLoader(getClass().getResource("prestations.fxml"));
        Stage stage = (Stage)((Node)event.getSource()).getScene().getWindow();
        Scene scene = new Scene(fxmlLoader.load(), 1280, 720);
        Image icon = new Image(Objects.requireNonNull(AKMController.class.getResourceAsStream("/assets/logo.png")));
        stage.getIcons().add(icon);
        stage.setTitle("AKM Gestion - Prestations");
        stage.setScene(scene);
        stage.show();
        getCatalog();
    }

    /**
     * Shows conversion page
     * @param event Button used to call function
     */
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

    /* API requests */

    /**
     * Sends login request to API using email and password
     */
    public void attemptLoginEmailPass() throws IOException {
        //String emailValue = loginEmail.getText();
        //String passwordValue = loginPassword.getText();
        String emailValue = "sohaib@akm.com";
        String passwordValue = "sohaib123";
        HashMap<String, String> params = new HashMap<>();
        params.put("email", emailValue);
        params.put("password", passwordValue);
        String body = JsonHandler.toJsonString(params);
        JSONObject response = AkmApi.requestApi(AkmApi.Actions.SIGN_IN, body);
        boolean success = response.getBoolean("success");
        if (success) {
            setPartnerID(response.getString("id_partner"));
            switchHome();
        } else {
            AkmException exception = AkmException.getExceptionFromCode(response.getInt("errorCode"));
            loginError.setText(exception.errorLabel + ".");
        }
    }

    /**
     * Gets catalog data for logged partner
     */
    public void getCatalog() {
        HashMap<String, String> params = new HashMap<>();
        params.put("mode", "chrono");
        params.put("reverse", "true");
        params.put("id_partner", getPartnerID());
        String body = JsonHandler.toJsonString(params);
        JSONObject response = AkmApi.requestApi(AkmApi.Actions.GET_CATALOG, body);
        boolean success = response.getBoolean("success");
        if (success) {
            setCatalog(response);
        } else {
            System.out.println(AkmException.getExceptionFromCode(response.getInt("errorCode")).errorLabel);
        }
    }

    /**
     * Sets-up prestations page
     * @param response Data from API
     */
    public void setCatalog(JSONObject response) {
        JSONArray presArr = response.getJSONArray("table");
        GridPane gridpane = new GridPane();
        gridpane.setBackground(new Background(new BackgroundFill(Color.AQUA, CornerRadii.EMPTY, Insets.EMPTY)));
        //for (int i=0; i<presArr.length()-1; i++) {
            //JSONObject presCurrent = presArr.getJSONObject(i);

            Label presName = new Label("Test");
            gridpane.add(presName, 0, 0);
        //} presCurrent.getString("name")
    }
}
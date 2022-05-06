package com.akm.front;

import com.akm.back.AkmApi;
import com.akm.back.AkmException;
import com.akm.back.JsonHandler;
import javafx.event.ActionEvent;
import javafx.event.EventHandler;
import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.geometry.Pos;
import javafx.scene.Node;
import javafx.scene.Scene;
import javafx.scene.control.Button;
import javafx.scene.control.Label;
import javafx.scene.control.PasswordField;
import javafx.scene.control.TextField;
import javafx.scene.image.Image;
import javafx.scene.layout.*;
import javafx.stage.Stage;
import org.json.JSONArray;
import org.json.JSONObject;

import java.io.IOException;
import java.util.HashMap;
import java.util.Objects;

public class AKMController {
    @FXML private static TextField loginEmail;
    @FXML private static PasswordField loginPassword;
    @FXML private static Label loginError;

    private static String token;
    private static String partnerID;
    private String getToken() {
        return token;
    }
    private String getPartnerID() {
        return partnerID;
    }
    private void setToken(String token) {
        AKMController.token = token;
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
        Stage stage = (Stage)((Node)event.getSource()).getScene().getWindow();
        Image icon = new Image(Objects.requireNonNull(AKMController.class.getResourceAsStream("/assets/logo.png")));
        stage.getIcons().add(icon);
        stage.setTitle("AKM Gestion - Prestations");

        HashMap<String, String> params = new HashMap<>();
        params.put("mode", "chrono");
        params.put("reverse", "true");
        params.put("id_partner", getPartnerID());
        String body = JsonHandler.toJsonString(params);
        JSONObject response = AkmApi.requestApi(AkmApi.Actions.GET_CATALOG, body);

        boolean success = response.getBoolean("success");
        if (success) {
            setCatalog(response, stage);
        } else {
            System.out.println(AkmException.getExceptionFromCode(response.getInt("errorCode")).errorLabel);
        }
    }

    /**
     * Shows edit prestation page
     * @param id Id of prestation to update
     * @param event Button used to call function
     * @param name Current name
     * @param description Current description
     * @param price Current price
     */
    public void switchUpdate(String id, javafx.event.ActionEvent event, String name, String description, double price) throws IOException {
        Stage stage = (Stage)((Node)event.getSource()).getScene().getWindow();
        Image icon = new Image(Objects.requireNonNull(AKMController.class.getResourceAsStream("/assets/logo.png")));
        stage.getIcons().add(icon);

        VBox root = new VBox();
        root.setAlignment(Pos.CENTER);

        TextField tfName = new TextField(name);
        TextField tfDesc = new TextField(description);
        TextField tfPrice = new TextField(String.valueOf(price));
        Button submit = new Button("Valider");
        submit.setOnAction(new EventHandler<ActionEvent>() {
            @Override public void handle(ActionEvent event) { try {
                System.out.println("Arrivée dans le setOnAction");
                updatePrestation(id, tfName.getText(), tfDesc.getText(), tfPrice.getText());
            } catch (IOException e) { e.printStackTrace(); } }
        });

        root.getChildren().add(tfName);
        root.getChildren().add(tfDesc);
        root.getChildren().add(tfPrice);
        root.getChildren().add(submit);

        Scene scene = new Scene(root, 1280, 720);
        stage.setScene(scene);
        stage.show();
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
            setToken(response.getString("token"));
            setPartnerID(response.getString("id_partner"));
            switchHome();
        } else {
            AkmException exception = AkmException.getExceptionFromCode(response.getInt("errorCode"));
            loginError.setText(exception.errorLabel + ".");
        }
    }

    /**
     * Sets-up prestations page
     * @param response Data from API
     * @param stage Stage where add the scene
     */
    public void setCatalog(JSONObject response, Stage stage) throws IOException {
        JSONArray presArr = response.getJSONArray("table");
        int length = presArr.length();
        GridPane gridpane = new GridPane();
        gridpane.setStyle("-fx-padding: 16;");
        gridpane.setHgap(10); gridpane.setVgap(10);
        gridpane.setGridLinesVisible(true);

        int numCols = 6;
        int numRows = length / numCols;
        for (int i=0; i<numCols; i++) {
            ColumnConstraints colConst = new ColumnConstraints();
            colConst.setPercentWidth(100.0 / numCols);
            gridpane.getColumnConstraints().add(colConst);
        }
        for (int i=0; i<numRows; i++) {
            RowConstraints rowConst = new RowConstraints();
            rowConst.setPercentHeight(100.0 / numRows);
            gridpane.getRowConstraints().add(rowConst);
        }

        int col=0, row=0;
        for (int i=0; i<length; i++) {
            JSONObject presCurrent = presArr.getJSONObject(i);

            if (i%4==0 && i!=0) { col=0; row++; } else col++;

            VBox vbox = new VBox(); vbox.setSpacing(10); vbox.setAlignment(Pos.CENTER_LEFT);
            HBox hbox = new HBox(); hbox.setSpacing(10);

            String presInfo = presCurrent.getString("name") + '\n'
                    + presCurrent.getString("description") + '\n'
                    + String.valueOf(presCurrent.getDouble("price"));
            Label presLabel = new Label(presInfo);
            Button presUpdate = new Button("Modifier");
            Button presDelete = new Button("Supprimer");

            presUpdate.setOnAction(new EventHandler<ActionEvent>() {
                @Override public void handle(ActionEvent event) { try {
                    switchUpdate(
                            String.valueOf(presCurrent.getInt("id")), event,
                            presCurrent.getString("name"), presCurrent.getString("description"), presCurrent.getDouble("price")
                    );
                } catch (IOException e) { e.printStackTrace(); } }
            });

            presDelete.setOnAction(new EventHandler<ActionEvent>() {
                @Override public void handle(ActionEvent event) { try {
                    deletePrestation(String.valueOf(presCurrent.getInt("id")), getToken());
                } catch (IOException e) { e.printStackTrace(); } }
            });

            hbox.getChildren().add(presUpdate); hbox.getChildren().add(presDelete);
            vbox.getChildren().add(presLabel); vbox.getChildren().add(hbox);

            gridpane.add(vbox, col, row);
        }

        VBox root = new VBox();
        root.setAlignment(Pos.CENTER);
        root.getChildren().add(gridpane);

        Scene scene = new Scene(root, 1280, 720);
        stage.setScene(scene);
        stage.show();
    }

    /**
     * Updates prestation
     * @param id ID of prestation
     * @param name New name
     * @param description New description
     * @param price New price
     */
    public void updatePrestation(String id, String name, String description, String price) throws IOException {
        System.out.println("Arrivée dans updatePrestation");
        HashMap<String, String> params = new HashMap<>();
        params.put("id", id);
        params.put("name", name);
        params.put("description", description);
        params.put("price", price);
        params.put("token", getToken());
        String body = JsonHandler.toJsonString(params);
        JSONObject response = AkmApi.requestApi(AkmApi.Actions.UPDATE, body);
        boolean success = response.getBoolean("success");
        if (success) {
            switchHome();
        } else {
            AkmException exception = AkmException.getExceptionFromCode(response.getInt("errorCode"));
            System.out.println(exception.errorLabel);
        }
    }

    /**
     * Deletes prestation
     * @param id ID of prestation
     * @param token User auth token
     */
    public void deletePrestation(String id, String token) throws IOException {
        HashMap<String, String> params = new HashMap<>();
        params.put("id", id);
        params.put("token", token);
        String body = JsonHandler.toJsonString(params);
        JSONObject response = AkmApi.requestApi(AkmApi.Actions.DELETE, body);
        boolean success = response.getBoolean("success");
        if (success) {
            switchHome();
        } else {
            AkmException exception = AkmException.getExceptionFromCode(response.getInt("errorCode"));
            System.out.println(exception.errorLabel);
        }
    }
}
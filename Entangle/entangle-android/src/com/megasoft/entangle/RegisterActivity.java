package com.megasoft.entangle;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import com.megasoft.config.Config;
import com.megasoft.requests.PostRequest;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Toast;

public class RegisterActivity extends Activity {

	private Button btnRegister;
	private Button btnLinkToSplash;
	private EditText username;
	private EditText email;
	private EditText password;
	private EditText confirmPassword;
	private TextView registerErrorMsg;
	JSONObject json = new JSONObject();

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_register);
		getActionBar().hide();
		username = (EditText) findViewById(R.id.register_username);
		email = (EditText) findViewById(R.id.register_email);
		password = (EditText) findViewById(R.id.register_password);
		confirmPassword = (EditText) findViewById(R.id.register_confirm_password);
		btnRegister = (Button) findViewById(R.id.btnRegister);
		btnLinkToSplash = (Button) findViewById(R.id.btnLinkToSplash);
		registerErrorMsg = (TextView) findViewById(R.id.registerErrorMsg);
		btnRegister.setOnClickListener(new View.OnClickListener() {

			@Override
			public void onClick(View v) {
				if (isEmpty(username) || isEmpty(email) || isEmpty(password)
						|| isEmpty(confirmPassword)) {
					return;
				}

				try {
					if (!isValidEmail(email.getText().toString())) {
						Toast.makeText(getApplicationContext(),
								"Error, not a valid email", Toast.LENGTH_SHORT)
								.show();
						return;
					} else if (shortPassword(password.getText().toString())) {

							Toast.makeText(
									getApplicationContext(),
									"Password should be more than six characters",
									Toast.LENGTH_SHORT).show();
							return;

						}
						

						else if (!passwordsMatch(password.getText().toString(),
								confirmPassword.getText().toString())) {
							Toast.makeText(getApplicationContext(),
									"Passwords do not match",
									Toast.LENGTH_SHORT).show();
							return;
						}
						else {

						json.put("username", username.getText().toString());
						json.put("email", email.getText().toString());
						json.put("password", password.getText().toString());
						json.put("confirmPassword", confirmPassword.getText()
								.toString());
					}
				} catch (JSONException e) {
					e.printStackTrace();
				}

				PostRequest request = new PostRequest(
						Config.API_BASE_URL_SERVER + "/register") {
					protected void onPostExecute(String response) {
						if (this.getStatusCode() == 201) {
							Toast.makeText(getApplicationContext(),
									"Registered Successfully!",
									Toast.LENGTH_LONG).show();
							goToLogin(response);
						} else if(this.getStatusCode() == 401){
							Toast.makeText(getApplicationContext(),
									"Not unique username",
									Toast.LENGTH_SHORT).show();
						}
						
						else if(this.getStatusCode() == 402){
							Toast.makeText(getApplicationContext(),
									"Not unique email",
									Toast.LENGTH_SHORT).show();
						}

					}

				};
				request.setBody(json);
				request.execute();

			}
		});

	}
	

	/*
	 * redirect the user to the splash activity once clicked
	 * @param: View view
	 * 
	 * 
	 * @author: Eslam Maged
	 */
	public void cancel(View view) {
		Intent intent = new Intent(this, SplashActivity.class);
		startActivity(intent);
		this.finish();
	}
	
	/*
	 * redirect the user to the login page once successfuly registered
	 * @param: String response
	 * 
	 * 
	 * @author: Eslam Maged
	 */

	public void goToLogin(String response) {
		startActivity(new Intent(this, LoginActivity.class));
		this.finish();
	}
	
	/*
	 * checks if the textbox is empty
	 * @param: EditText editText
	 * @return: boolean, true if empty. false otherwise.
	 * 
	 * @author: Eslam Maged
	 */

	private boolean isEmpty(EditText editText) {
		if (editText.getText().toString().length() == 0) {
			editText.setError("This Field is Required");
			return true;
		}
		editText.setError(null);
		return false;
	}
	
	/*
	 * Validates that a certain email is in a correct format
	 * @param the email to be validated
	 * @return true if the email is in a valid format.
	 * 
	 * @author: Eslam Maged
	 */

	private boolean isValidEmail(String email) {
		String regex = "^[_a-z0-9-]+(\\.[_a-z0-9-]+)*@[a-z0-9-]+(\\.[a-z0-9-]+)*(\\.[a-z]{2,4})$";
		return email.matches(regex);
	}
	
	/*
	 * checks if the two given passwords match
	 * @param: String password
	 * @param: String confirmPassword
	 * @return: boolean, true if short. false otherwise.
	 * 
	 * @author: Eslam Maged
	 */

	private boolean passwordsMatch(String password, String confirmPassword) {
		if (password.equals(confirmPassword)) {
			return true;
		} else
			return false;
	}
	/*
	 * checks for the length of the password
	 * @param: String password
	 * @return: boolean, true if short. false otherwise.
	 * 
	 * @author: Eslam Maged
	 */

	private boolean shortPassword(String password) {
		if (password.length() < 7) {
			return true;
		} else {
			return false;
		}
	}
}

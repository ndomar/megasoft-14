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
		
		//Importing all the assets like textfields.
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
				if(isEmpty(username) || isEmpty(email) || isEmpty(password) || isEmpty(confirmPassword)) {
					return;
				}
				
				try {
					json.put("username", username.getText().toString());
					json.put("email", email.getText().toString());
					json.put("password", password.getText().toString());
					json.put("confirmPassword", confirmPassword.getText().toString());
				}
				catch(JSONException e) {
					e.printStackTrace();
				}
				
				PostRequest request = new PostRequest(Config.API_BASE_URL_SERVER + "/register") {
					protected void onPostExecute(String response) {
						if(this.getStatusCode() == 201) {
							Toast.makeText(getApplicationContext(), "Success!", Toast.LENGTH_LONG).show();
							goToLogin(response);
							}
						else {
							Toast.makeText(getApplicationContext(), "Error, cannot create request", Toast.LENGTH_SHORT).show();
						}
					}

					
				};
				request.setBody(json);
				request.execute();
				
			}
		});
		
	}
	
	public void cancel(View view) {
		Intent intent = new Intent(this, SplashActivity.class);
		startActivity(intent);
		this.finish();
	}
	
	/*public void register(View view) {
		
	}*/
	public void goToLogin(String response) {
			startActivity(new Intent(this, LoginActivity.class));
			this.finish();
	}
	private boolean isEmpty(EditText editText) {
		if (editText.getText().toString().length() == 0) {
			editText.setError("This Field is Required");
			return true;
		}
		editText.setError(null);
		return false;
	}
}

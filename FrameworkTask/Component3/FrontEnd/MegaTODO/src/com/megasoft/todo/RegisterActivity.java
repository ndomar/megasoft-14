package com.megasoft.todo;

import org.json.JSONException;
import org.json.JSONObject;

import com.example.megatodo.R;
import com.example.megatodo.R.layout;
import com.example.megatodo.R.menu;
import com.megasoft.todo.http.HTTPPostRequest;

import android.os.Bundle;
import android.app.Activity;
import android.content.Intent;
import android.view.Menu;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;

public class RegisterActivity extends Activity {

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_register);
		
		final EditText username = (EditText) findViewById(R.id.usernameEditText);
		final EditText password = (EditText) findViewById(R.id.passwordEditText);
		final Button register 	= (Button) findViewById(R.id.register);
		final JSONObject json = new JSONObject();
		
		
		register.setOnClickListener(new View.OnClickListener() {
			
			@Override
			public void onClick(View arg0) {
				try {
					json.put("username", username.getText().toString());
					json.put("password", password.getText().toString());
				} catch (JSONException e) {
					e.printStackTrace();
				}
				(new HTTPPostRequest(){
					
					protected void onPostExecute(String res) {
						if (res.equals("ERROR")) {
							displayError();
						} else {
							redirectToLogin();
						}
					}
					
				}).execute(json.toString(), "/users");
			}
		});
		
		
	}
	
	private void redirectToLogin() {
		Intent intent = new Intent(this, LoginActivity.class);
		startActivity(intent);
	}
	
	private void displayError() {
		TextView error = (TextView) findViewById(R.id.registerError);
		error.setText("Registration failed !");
	}

}

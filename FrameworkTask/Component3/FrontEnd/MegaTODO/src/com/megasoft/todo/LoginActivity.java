package com.megasoft.todo;

import org.json.JSONException;
import org.json.JSONObject;

import com.example.megatodo.R;
import com.megasoft.todo.http.HTTPPostRequest;

import android.os.Bundle;
import android.app.Activity;
import android.util.Log;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Toast;
import android.text.Editable;
import android.text.TextWatcher;
import android.annotation.TargetApi;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Build;

public class LoginActivity extends Activity {
	private EditText username;
	private EditText password;
	private Button login;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_login);
		registerViews();
	}

	private void redirectToListActivity() {
		Intent intent = new Intent(this, MainActivity.class);
		startActivity(intent);
	}
	
	private void displayError() {
		TextView error = (TextView) findViewById(R.id.errorText);
		error.setText("Authentication failed");
	}
	
	private void createSession(String sessionId) {
		SharedPreferences settings = getSharedPreferences("AppConfig", 0);
        SharedPreferences.Editor editor = settings.edit();
        editor.putString("sessionId", sessionId);
        editor.commit();

	}
	
	private void registerViews() {
		username = (EditText) findViewById(R.id.usernameText);
		password = (EditText) findViewById(R.id.passwordText);
		login = (Button) findViewById(R.id.login);
		
		
		login.setOnClickListener(new View.OnClickListener() {
			
			@Override
			public void onClick(View v) {
				JSONObject json = new JSONObject();
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
							JSONObject resJson;
							try {
								resJson = new JSONObject(res);
								createSession(resJson.getString("id"));
							} catch (JSONException e) {
								e.printStackTrace();
							}
							redirectToListActivity();
						}
					}
					
		        }).execute(json.toString(), "/sessions");
				
			}
		});
	}
	
	@Override
    public boolean onCreateOptionsMenu(Menu menu) {
        // Inflate the menu; this adds items to the action bar if it is present.
        getMenuInflater().inflate(R.menu.login, menu);
        return true;
    }
    
    public void redirectToRegister(MenuItem m) {
    	Intent intent = new Intent(this, RegisterActivity.class);
    	startActivity(intent);
    }
}

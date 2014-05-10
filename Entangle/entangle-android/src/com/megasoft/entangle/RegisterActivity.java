package com.megasoft.entangle;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;

public class RegisterActivity extends Activity {
	
	private Button btnRegister;
	private Button btnLinkToSplash;
	private EditText inputUsername;
	private EditText inputEmail;
	private EditText inputPassword;
	private EditText inputConfirmPassword;
	private TextView registerErrorMsg;
	
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_register);
		getActionBar().hide();
		
		//Importing all the assets like textfields.
		inputUsername = (EditText) findViewById(R.id.register_username);
		inputEmail = (EditText) findViewById(R.id.register_email);
		inputPassword = (EditText) findViewById(R.id.register_password);
		inputConfirmPassword = (EditText) findViewById(R.id.register_confirm_password);
		btnRegister = (Button) findViewById(R.id.btnRegister);
		btnLinkToSplash = (Button) findViewById(R.id.btnLinkToSplash);
		registerErrorMsg = (TextView) findViewById(R.id.registerErrorMsg);
	}
	
	public void cancel(View view) {
		Intent intent = new Intent(this, SplashActivity.class);
		startActivity(intent);
		this.finish();
	}
	
	public void register(View view) {
		
	}
}

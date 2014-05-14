package com.megasoft.entangle;

import org.json.JSONException;
import org.json.JSONObject;

import com.megasoft.config.Config;
import com.megasoft.requests.PostRequest;

import android.app.Activity;
import android.os.Bundle;
import android.widget.EditText;
import android.widget.Toast;

public class ForgetPasswordActivity extends Activity {
	/**
	 * email of the user
	 */
	EditText email;
	
	public final String FORGET = "/user/forgetPass";
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_forget_password);
		
	}
	
	/**
	 * this function is responsible for getting the user name and email from the user and sending the password reset request
	 * @author KareemWahby
	 */
	public void forgetPassword() {
		email=(EditText) findViewById(R.id.emailFroget);
		
		JSONObject json = new JSONObject();
		try {
			json.put("email", email.getText().toString());
		} catch (JSONException e) {
			e.printStackTrace();
		}
		
		PostRequest request = new PostRequest(Config.API_BASE_URL_SERVER
				+ FORGET) {
			@Override
			protected void onPostExecute(String response) {
				if(this.getStatusCode()!=200){
					Toast.makeText(getBaseContext(), "Please enter a valid e-mail!",
							Toast.LENGTH_SHORT).show();
				}else{
					Toast.makeText(getBaseContext(), "Email Sent Successfully!, Check you mail",
							Toast.LENGTH_SHORT).show();
					finish();
				}
			}
		};
		request.setBody(json);
		request.execute();
	}
}

package com.megasoft.entangle;

import org.json.JSONException;
import org.json.JSONObject;

import com.megasoft.config.Config;
import com.megasoft.requests.PostRequest;

import android.app.Activity;
import android.app.ActionBar;
import android.app.Fragment;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.ViewGroup;
import android.widget.EditText;
import android.widget.TextView;
import android.os.Build;

public class ForgetPasswordActivity extends Activity {

	EditText email;
	EditText name;
	public final String FORGET = "/user/forgetPass";
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_forget_password);
		
	}
	public void forgetPassword() {
		email=(EditText) findViewById(R.id.emailFroget);
		name=(EditText) findViewById(R.id.nameForget);
		
		JSONObject json = new JSONObject();
		try {
			json.put("name", name.getText().toString());
			json.put("email", email.getText().toString());
		} catch (JSONException e) {
			e.printStackTrace();
		}
		
		PostRequest request = new PostRequest(Config.API_BASE_URL_SERVER
				+ FORGET) {
			@Override
			protected void onPostExecute(String response) {
				if (this.getStatusCode() == 200) {
					//send email to user here
				} else {
					
				}

			}
		};
		request.setBody(json);
		request.execute();
	}
}

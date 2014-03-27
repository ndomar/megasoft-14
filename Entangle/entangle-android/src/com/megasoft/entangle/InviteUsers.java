package com.megasoft.entangle;

import java.util.ArrayList;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.app.Activity;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.text.InputType;
import android.view.Menu;
import android.view.View;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.Toast;
import android.widget.LinearLayout.LayoutParams;

import com.megasoft.config.Config;
import com.megasoft.requests.PostRequest;

public class InviteUsers extends Activity {
	
	int tangleId;
	String sessionId;
	SharedPreferences settings;
	
	LinearLayout layout;
	
	ArrayList<EditText> editTexts;
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_invite_users);
		
		this.tangleId = getIntent()
					.getIntExtra("com.megasoft.entangle.tangleId", -1);
		
		this.settings = getSharedPreferences(Config.SETTING,0);
		this.sessionId = settings.getString(Config.SESSION_ID, "");
		
		this.layout = (LinearLayout)findViewById(R.id.invite_emails);
		
		this.editTexts = new ArrayList<EditText>();
		
		this.addEmailField(null);
		
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.invite_users, menu);
		return true;
	}
	
	public void addEmailField(View view){
		
		EditText newEditText = new EditText(this);
		newEditText.setHint(R.string.user_email);
		newEditText.setInputType(InputType.TYPE_TEXT_VARIATION_EMAIL_ADDRESS);
		editTexts.add(newEditText);
		layout.addView(newEditText, new LinearLayout.LayoutParams(LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT));
	}
	
	public void goToConfirmationActivity(View view){
		JSONArray emails = new JSONArray();
		for(EditText emailEditText : editTexts){
			String val = emailEditText.getText().toString();
			if(val.equals("")){
				continue;
			}else{
				emails.put(val);
			}
		}
		
		JSONObject request = new JSONObject();
		try {
			request.put("emails", emails);
		} catch (JSONException e) {
			e.printStackTrace();
		}
		
		PostRequest postRequest = new PostRequest(Config.API_BASE_URL+"/tangle/"+tangleId+"/check-membership"){
			public void onPostExecute(String response){
				showSuccessMessage();
			}
		};
		
		postRequest.addHeader(Config.API_SESSION_ID, sessionId);
		postRequest.setBody(request);
		postRequest.execute();
	}
	
	public void showSuccessMessage(){
		Toast.makeText(this, "Sent !", Toast.LENGTH_LONG).show();
	}

}

package com.megasoft.entangle;

import java.lang.reflect.Array;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Hashtable;
import java.util.List;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import com.megasoft.requests.GetRequest;
import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.view.Menu;
import android.view.View;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;

public class ProfileActivity extends Activity {
	private TextView name;
	private TextView descr;
	private TextView balance;
	private TextView birthdate;
	private TextView transaction;
	private ImageView verifiedView;
	final Activity self = this;
    LinearLayout layoutContainer;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_profile);
			try {
					viewProfile();
				} catch (JSONException e) {
					e.printStackTrace();
				}
			
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		getMenuInflater().inflate(R.menu.main, menu);
		return true;
	}
	
	public void viewProfile() throws JSONException {
		layoutContainer = (LinearLayout) this.findViewById(R.id.layout_container);
		name = (TextView) findViewById(R.id.nameView);
		descr = (TextView) findViewById(R.id.descriptionView);
		balance = (TextView) findViewById(R.id.balanceView);
		birthdate = (TextView) findViewById(R.id.birthdateView);
		verifiedView = (ImageView) findViewById(R.id.verifiedView);
		
		Intent intent = getIntent();
		int tangleId = intent.getIntExtra("tangle id", -1);
		int userId = intent.getIntExtra("user id", -1);
		
		String link = "http://entangle2.apiary-mock.com/tangle/" + tangleId + "/user/" + userId + "/profile";
		getInformation(link);
	}
	
	public Hashtable<String, Object> getInformation(String link) {
		 final Hashtable<String, Object> userInformation = new Hashtable<String, Object>();
		GetRequest request = new GetRequest(link) {
			protected void onPostExecute(String response) {  
				try {
						JSONObject jSon = new JSONObject(response);
						JSONObject information =  jSon.getJSONObject("information");
						JSONArray array = jSon.getJSONArray("transactions");

						userInformation.put("loggedIn", jSon.getBoolean("loggedIn"));
						userInformation.put("name", information.getString("name"));
						userInformation.put("description", information.getString("Description"));
						userInformation.put("credit", information.getString("balance"));
						userInformation.put("birthdate", information.getString("birthdate"));
						userInformation.put("verified",information.getBoolean("verified"));
						
						for(int i = 0; i < array.length(); i++) { 
							JSONObject object = array.getJSONObject(i);
							String requester = object.getString("requesterName");
							String request = object.getString("requestDescription");
							String amount = object.getString("amount");
							transaction = new TextView(self);
							transaction.setText("Requester: " + requester 
									+ '\n' + "Request: " + request
									+ '\n' + "Amount: " + amount);
							layoutContainer.addView(transaction);
							}
						} catch (JSONException e) {
								e.printStackTrace();
								}
				}
			};
			request.execute();
			return userInformation;	
	}

}

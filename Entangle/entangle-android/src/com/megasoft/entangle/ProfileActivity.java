package com.megasoft.entangle;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import com.megasoft.config.Config;
import com.megasoft.requests.GetRequest;
import android.app.Activity;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.view.Menu;
import android.view.View;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;

public class ProfileActivity extends Activity {
	private TextView name;
	private TextView descr;
	private TextView balance;
	private TextView birthdate;
	private TextView edit;
	private ImageView verifiedView;
    LinearLayout layoutContainer;
	SharedPreferences settings;
	final Activity self = this;
	int loggedInId;
	
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
		edit = (TextView) findViewById(R.id.EditProfile);
		name = (TextView) findViewById(R.id.nameView);
		balance = (TextView) findViewById(R.id.balanceView);
		descr = (TextView) findViewById(R.id.descriptionView);
		birthdate = (TextView) findViewById(R.id.birthdateView);
		verifiedView = (ImageView) findViewById(R.id.verifiedView);
		layoutContainer = (LinearLayout) this.findViewById(R.id.layout_container);
		this.settings = getSharedPreferences(Config.SETTING, 0);
		this.loggedInId = settings.getInt(Config.USER_ID, -1);
		Intent intent = getIntent();
		int tangleId = intent.getIntExtra("tangle id", -1);
		int userId = intent.getIntExtra("user id", -1);
		setInformation(userId, tangleId);
	}
	
	public void setInformation(final int userId, int tangleId) {
		String link = "http://entangle2.apiary-mock.com/tangle/" 
	+ tangleId + "/user/" + userId + "/profile";
		GetRequest request = new GetRequest(link) {
			protected void onPostExecute(String response) {
				JSONObject jSon;
				try {
					jSon = new JSONObject(response);
					JSONArray transactions = jSon.getJSONArray("transactions");
					JSONObject information =  jSon.getJSONObject("information");
					setTransactions(transactions);
					name.setText(information.getString("name"));
					descr.setText("Description: " + information.getString("Description"));
					balance.setText("Credit: " + information.getString("balance") + " points");
					birthdate.setText("Birthdate: " + information.getString("birthdate"));
					boolean verified = information.getBoolean("verified");
					if (verified) {
							verifiedView.setVisibility(View.VISIBLE);
							} 
					if(loggedInId == userId) {
						edit.setVisibility(View.VISIBLE);
						edit.setOnClickListener(new View.OnClickListener() {
							@Override
				            public void onClick(View v) {
				            	goToEditProfile();
				            	}
				            });
						}
					} catch (JSONException e) {
						e.printStackTrace();
						}
				}
			};
			request.execute();
			}
	
	public void setTransactions(JSONArray transactions) {
		for(int i = 0; i < transactions.length(); i++) {
			JSONObject object;
			try {
				object = transactions.getJSONObject(i);
				TextView transaction = new TextView(self);
				String requester = object.getString("requesterName");
				String request = object.getString("requestDescription");
				String amount = object.getString("amount");
				transaction.setText("Requester: " + requester 
						+ '\n' + "Request: " + request
						+ '\n' + "Amount: " + amount);
				layoutContainer.addView(transaction);
				} catch (JSONException e) {
					e.printStackTrace();
					}
			}
		}
	
	public void goToEditProfile() {
		Intent editProfile = new Intent(this, EditProfileActivity.class);
		editProfile.putExtra("user id", loggedInId);
		startActivity(editProfile);
	}
	}
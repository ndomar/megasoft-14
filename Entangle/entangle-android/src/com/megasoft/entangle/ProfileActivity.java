package com.megasoft.entangle;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import com.megasoft.requests.GetRequest;
import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.Menu;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;

public class ProfileActivity extends Activity {
	private TextView name;
	private TextView descr;
	private TextView balance;
	private TextView birthdate;
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
		name = (TextView) findViewById(R.id.nameView);
		balance = (TextView) findViewById(R.id.balanceView);
		descr = (TextView) findViewById(R.id.descriptionView);
		birthdate = (TextView) findViewById(R.id.birthdateView);
		verifiedView = (ImageView) findViewById(R.id.verifiedView);
		layoutContainer = (LinearLayout) this.findViewById(R.id.layout_container);
		
		Intent intent = getIntent();
		int tangleId = intent.getIntExtra("tangle id", -1);
		int userId = intent.getIntExtra("user id", -1);
		
		String link = "http://entangle2.apiary-mock.com/tangle/" 
		+ tangleId + "/user/" + userId + "/profile";
		setInformation(link);
	}
	
	public void setInformation(String link) {
		GetRequest request = new GetRequest(link) {
			protected void onPostExecute(String response) {
				Log.e("7mada", response);
				JSONObject jSon;
				try {
					jSon = new JSONObject(response);
			//		boolean loggedIn = jSon.getBoolean("loggedIn");
					JSONArray transactions = jSon.getJSONArray("transactions");
					JSONObject information =  jSon.getJSONObject("information");
					setTransactions(transactions);
					name.setText(information.getString("name"));
					descr.setText("Description: " + information.getString("Description"));
					balance.setText("Credit: " + information.getString("balance") + " points");
					birthdate.setText("Birthdate: " + information.getString("birthdate"));
					boolean verified = information.getBoolean("verified");
					
					if (verified) {
							verifiedView.setVisibility(1);
							} else {
								verifiedView.setVisibility(0);
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
	}
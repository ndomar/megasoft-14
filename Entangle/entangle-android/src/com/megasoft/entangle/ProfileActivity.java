package com.megasoft.entangle;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import com.megasoft.config.Config;
import com.megasoft.requests.GetRequest;

import android.content.SharedPreferences;
import android.os.Bundle;
import android.support.v4.app.FragmentActivity;
import android.support.v4.app.FragmentTransaction;
import android.util.Log;
import android.view.View;
import android.widget.LinearLayout;
import android.widget.ScrollView;
import android.widget.Toast;

public class ProfileActivity extends FragmentActivity {
	
	private int userId;
	private int tangleId;
	private SharedPreferences settings;
	private String sessionId;
	private ScrollView scrollView;

	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_profile);
		ProfileFragment profile = new ProfileFragment();
		Bundle bundle = new Bundle();
		this.tangleId = getIntent().getIntExtra("tangleId", 2);
		this.userId = getIntent().getIntExtra("userId", 0);
		this.settings = getSharedPreferences(Config.SETTING, 0);
		this.sessionId = settings.getString(Config.SESSION_ID, "");
		bundle.putInt("tangleId", tangleId);
		bundle.putInt("userId", userId);
		bundle.putBoolean("general", false);
		profile.setArguments(bundle);
		FragmentTransaction transaction = getSupportFragmentManager()
				.beginTransaction();
		transaction.add(R.id.profile_layout, profile);
		transaction.commit();
		GetTransactions();
		
	}
	
	private void GetTransactions() {
		String link = Config.API_BASE_URL_SERVER + "/tangle/" + tangleId + "/user/" + userId + "/transactions";
		GetRequest request = new GetRequest(link) {
			@Override
			protected void onPostExecute(String response) {
				if (this.getStatusCode() == 200) {
					try {
						JSONObject jSon = new JSONObject(response); 	
						viewTransactions(jSon.getJSONArray("transactions"));
					} catch (JSONException e) {
						e.printStackTrace();
					}
				} else {
				}
			}
		};
		request.addHeader("X-SESSION-ID", this.sessionId);
		request.execute();
	}
	
	private void viewTransactions(JSONArray transactions) {
		LinearLayout transactions_layout = ((LinearLayout) findViewById(R.id.transactions_layout));
		scrollView = (ScrollView) findViewById(R.id.transactions_scroll_view);
		if (transactions.length() > 0) {
			transactions_layout.setVisibility(View.VISIBLE);
		}

		for (int i = 0; i < transactions.length(); i++) {
			try {
				JSONObject transaction = transactions.getJSONObject(i);
				TransactionEntryFragment entry = new TransactionEntryFragment();
				entry.setOfferer(transaction.getString("offererName"));
				entry.setRequester(transaction.getString("requesterName"));
				entry.setOffererId(transaction.getInt("offererId"));
				entry.setRequesterId(transaction.getInt("requesterId"));
				entry.setAmount(transaction.getInt("amount"));
				entry.setTangleId(tangleId);
				getSupportFragmentManager().beginTransaction()
						.add(R.id.transactions_layout, entry).commit();
			} catch (JSONException e) {
				e.printStackTrace();
			}
		}
		scrollView.postDelayed(new Runnable() {

			@Override
			public void run() {
				scrollView.fullScroll(ScrollView.FOCUS_UP);
			}
		}, 500);
	}
}

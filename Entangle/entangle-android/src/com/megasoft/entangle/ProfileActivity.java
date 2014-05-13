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
import android.view.View;
import android.widget.LinearLayout;
import android.widget.ScrollView;
import android.widget.TextView;
import com.megasoft.entangle.megafragments.*;


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
		this.tangleId = getIntent().getIntExtra("tangleId", -1);
		this.userId = getIntent().getIntExtra("userId", -1);
		this.settings = getSharedPreferences(Config.SETTING, -1);
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

	/**
	 * Gets the transactions of the user
	 * Calls viewTransactions(JSONArray transactions) method
	 * @author Almgohar
	 */

	private void GetTransactions() {
		String link = Config.API_BASE_URL_SERVER + "/tangle/" + tangleId + "/user/" + userId + "/transactions";
		GetRequest request = new GetRequest(link) {
			@Override
			protected void onPostExecute(String response) {
				if (this.getStatusCode() == 200) {
					try {
						JSONObject jSon = new JSONObject(response); 
						((TextView)findViewById(R.id.credit)).setText("" + jSon.getInt("credit"));
						viewTransactions(jSon.getJSONArray("transactions"));
					} catch (JSONException e) {
						e.printStackTrace();
					}
				} else {
				}
			}
		};
		
		request.addHeader(Config.API_SESSION_ID, this.sessionId);
		request.execute();
	}
	
	/**
	 * Views the user transactions
	 * @param transactions
	 * @author Almgohar
	 */
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
				entry.setImageURL(transaction.getString("photo"));
				entry.setRequester(transaction.getString("requesterName"));
				entry.setRequestId(transaction.getInt("requestId"));
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

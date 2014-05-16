package com.megasoft.entangle;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.app.Activity;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.support.v4.app.FragmentActivity;
import android.support.v4.app.FragmentTransaction;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.LinearLayout;
import android.widget.ScrollView;
import android.widget.TextView;
import android.widget.Toast;

import com.megasoft.config.Config;
import com.megasoft.requests.DeleteRequest;
import com.megasoft.requests.GetRequest;
import com.megasoft.utils.UI;

public class ProfileActivity extends FragmentActivity {
	
	private int userId;
	private int tangleId;

	private SharedPreferences settings;
	private String sessionId;
	private ScrollView scrollView;
	Activity activity = null;
	MenuItem deleteItem = null;
	boolean isTangleOwner = false;
	
	public MenuItem getDeleteItem() {
		return deleteItem;
	}

	public void setDeleteItem(MenuItem deleteItem) {
		this.deleteItem = deleteItem;
	}

	public boolean isTangleOwner() {
		return isTangleOwner;
	}

	public void setTangleOwner(boolean isTangleOwner) {
		this.isTangleOwner = isTangleOwner;
	}
	
	public Activity getActivity() {
		return activity;
	}

	public void setActivity(Activity activity) {
		this.activity = activity;
	}

	public int getUserId() {
		return userId;
	}

	public int getTangleId() {
		return tangleId;
	}

	public String getSessionId() {
		return sessionId;
	}
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_profile);
		setActivity(this);
		ProfileSuperFragment profile = new ProfileSuperFragment();
		Bundle bundle = new Bundle();
		this.tangleId = getIntent().getIntExtra("tangleId", -1);
		this.userId = getIntent().getIntExtra("userId", -1);
		bundle.putInt("tangleId", tangleId);
		bundle.putInt("userId", userId);
		bundle.putBoolean("general", false);
		profile.setArguments(bundle);
		FragmentTransaction transaction = getSupportFragmentManager()
				.beginTransaction();
		transaction.add(R.id.profile, profile);
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
	
	private void sendRemoveUserRequest(){
		DeleteRequest deleteRequest = new DeleteRequest(
				Config.API_BASE_URL + "/tangle/" + getTangleId() + "/user/" + getUserId()){
			protected void onPostExecute(String response){
				if (!this.hasError()){
					getActivity().finish();
				} else{
					UI.makeToast(getActivity(), 
							"Something went wrong, Please try again.", 
							Toast.LENGTH_SHORT);
				}
			}
		};
		deleteRequest.addHeader(Config.API_SESSION_ID, getSessionId());
		deleteRequest.execute();
	}
	
	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.

		getMenuInflater().inflate(R.menu.view_profile, menu);
		return true;
	}
	
	@Override
	public boolean onOptionsItemSelected(MenuItem item) {
	     
	 	 switch (item.getItemId()) {
	 	 	case R.id.removeUserOption:
	 	 		sendRemoveUserRequest();
	 	 		return true;
	 	    default:
	 	        return super.onOptionsItemSelected(item);
	 	 }

	}
}

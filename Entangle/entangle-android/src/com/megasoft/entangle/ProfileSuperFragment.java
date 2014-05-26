package com.megasoft.entangle;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import com.megasoft.config.Config;
import com.megasoft.requests.GetRequest;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentManager;
import android.support.v4.app.FragmentTransaction;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.LinearLayout;
import android.widget.ScrollView;
import android.widget.TextView;
import android.widget.Toast;

public class ProfileSuperFragment extends Fragment {

	private View view;

	/**
	 * The user id
	 */
	private int userId;
	
	/**
	 * The tangle id
	 */
	private int tangleId;
	
	/**
	 * The session id
	 */
	private String sessionId;
	
	/**
	 * The ScrollView containing the transactions
	 */
	private ScrollView scrollView;
	
	/**
     * The preferences instance
     */
	private SharedPreferences settings;

	private boolean isDestroyed;
	
	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container,
			Bundle savedInstanceState) {
		view = inflater.inflate(R.layout.super_profile, container, false);
		FragmentManager fm = getFragmentManager();
		FragmentTransaction ft = fm.beginTransaction();
		ProfileFragment profile = new ProfileFragment();
		Bundle profileArguments = new Bundle();
		tangleId = getArguments().getInt("tangleId");
		userId = getArguments().getInt("userId");
		this.settings = getActivity().getSharedPreferences(Config.SETTING, 0);
		sessionId = settings.getString(Config.SESSION_ID, "");
		profileArguments.putInt("tangleId", tangleId);
		profileArguments.putInt("userId", userId);
		profileArguments.putBoolean("general", false);
		profile.setArguments(profileArguments);
		ft.add(R.id.profile_layout, profile);
		ft.commit();
		GetTransactions();

		return view;
	}

	/**
	 * Gets the transactions of the user Calls viewTransactions(JSONArray
	 * transactions) method
	 * @author Almgohar
	 */
	private void GetTransactions() {
		String link = Config.API_BASE_URL + "/tangle/" + tangleId + "/user/" + userId + "/transactions";
		GetRequest request = new GetRequest(link) {
			@Override
			protected void onPostExecute(String response) {
				if(isDestroyed){
					return;
				}
				if (this.getStatusCode() == 200) {
					try {
						JSONObject jSon = new JSONObject(response); 
						((TextView)view.findViewById(R.id.profile_layout).findViewById(R.id.credit)).setText("" + jSon.getInt("credit"));
						viewTransactions(jSon.getJSONArray("transactions"));
					} catch (JSONException e) {
						e.printStackTrace();
					}
				} else {
					Toast toast = Toast.makeText(getActivity().getApplicationContext(),"Something went wrong",Toast.LENGTH_LONG);
					toast.show();
				}
			}
		};
		
		request.addHeader(Config.API_SESSION_ID, this.sessionId);
		request.execute();
	}

	/**
	 * Views the user transactions
	 * @param JSonArray transactions
	 * @author Almgohar
	 */
	private void viewTransactions(JSONArray transactions) {
		
		LinearLayout transactions_layout = ((LinearLayout) view.findViewById(R.id.profile_layout).findViewById(R.id.transactions_layout));
		scrollView = (ScrollView) view.findViewById(R.id.profile_layout).findViewById(R.id.transactions_scroll_view);
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
				getFragmentManager().beginTransaction()
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
	
	public void onPause() {
		super.onPause();
		isDestroyed = true;
	}
}

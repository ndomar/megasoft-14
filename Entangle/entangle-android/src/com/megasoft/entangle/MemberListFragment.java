package com.megasoft.entangle;

import java.util.ArrayList;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.app.AlertDialog;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentTransaction;
import android.text.Editable;
import android.text.TextWatcher;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;

import com.megasoft.config.Config;
import com.megasoft.requests.GetRequest;

/*
 * A fragment for the member list.
 * @author Omar ElAzazy
 */
public class MemberListFragment extends Fragment {

	private int tangleId;
	private String sessionId;
	private String[] memberNames;
	private int[] memberBalances;
	private String[] memberAvatarURLs;
	private int[] memberIds;
	private LinearLayout memberListView;
	private int numberOfMembers;
	private ViewGroup container;
	private EditText searchBar;
	private ArrayList<MemberEntryFragment> memberFragments = new ArrayList<MemberEntryFragment>();
	private TextView noMembers;
	private boolean isDestroyed;

	public ViewGroup getContainer() {
		return container;
	}

	public void setContainer(ViewGroup container) {
		this.container = container;
	}

	public int getNumberOfMembers() {
		return numberOfMembers;
	}

	public void setNumberOfMembers(int numberOfMembers) {
		this.numberOfMembers = numberOfMembers;
	}

	public LinearLayout getMemberListView() {
		return memberListView;
	}

	public void setMemberListView(LinearLayout memberListView) {
		this.memberListView = memberListView;
	}

	public String[] getMemberNames() {
		return memberNames;
	}

	public void setMemberNames(String[] memberNames) {
		this.memberNames = memberNames;
	}

	public int[] getMemberBalances() {
		return memberBalances;
	}

	public void setMemberBalances(int[] memberBalances) {
		this.memberBalances = memberBalances;
	}

	public String[] getMemberAvatarURLs() {
		return memberAvatarURLs;
	}

	public void setMemberAvatarURLs(String[] memberAvatarURLs) {
		this.memberAvatarURLs = memberAvatarURLs;
	}

	public int[] getMemberIds() {
		return memberIds;
	}

	public void setMemberIds(int[] memberIds) {
		this.memberIds = memberIds;
	}

	public String getSessionId() {
		return sessionId;
	}

	public void setSessionId(String sessionId) {
		this.sessionId = sessionId;
	}

	public int getTangleId() {
		return tangleId;
	}

	public void setTangleId(int tangleId) {
		this.tangleId = tangleId;
	}

	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container,
			Bundle savedInstanceState) {
		setSessionId(getActivity().getSharedPreferences(Config.SETTING, 0)
				.getString(Config.SESSION_ID, ""));
		setTangleId(getArguments().getInt(Config.TANGLE_ID));

		View view = inflater.inflate(R.layout.fragment_member_list, container,
				false);

		setContainer(container);
		setMemberListView((LinearLayout) view
				.findViewById(R.id.view_member_list));
		searchBar = (EditText) view.findViewById(R.id.search_field);
		noMembers = new TextView(getActivity().getBaseContext());
		noMembers.setText(getString(R.string.member_not_found));
		noMembers.setVisibility(View.GONE);
		getMemberListView().addView(noMembers);
		noMembers.setVisibility(View.GONE);
		fetchMembers();
		searchBar.addTextChangedListener(new TextWatcher() {
			@Override
			public void afterTextChanged(Editable arg0) {
				// TODO Auto-generated method stub
			}

			@Override
			public void beforeTextChanged(CharSequence arg0, int arg1,
					int arg2, int arg3) {
				// TODO Auto-generated method stub
			}

			@Override
			public void onTextChanged(CharSequence arg0, int arg1, int arg2,
					int arg3) {
				searchMembers(arg0.toString());
			}
		});

		return view;
	}

	/*
	 * It makes a request getting members and creating the fragments for member
	 * entries to be viewed
	 * 
	 * @author Omar ElAzazy
	 */
	private void fetchMembers() {

		GetRequest getRequest = new GetRequest(Config.API_BASE_URL_SERVER
				+ "/tangle/" + getTangleId() + "/user") {
			public void onPostExecute(String response) {
				if(isDestroyed){
					return;
				}
				Log.e("test", this.getStatusCode() + ""); // ///////////////////////////////

				if (!this.hasError() && this.getStatusCode() == 200) {
					if (!showData(response)) {
						toasterShow("Something went wrong, please try again later");
					}
				} else {
					toasterShow("Something went wrong, please try again later");
				}
			}
		};

		getRequest.addHeader(Config.API_SESSION_ID, sessionId);
		getRequest.execute();
	}

	/*
	 * It creates the fragments to be viewed in the list
	 * 
	 * @param String response, the body of the response of the request to get
	 * all members in a tangle
	 * 
	 * @return a boolean indicating if this was successful or not
	 * 
	 * @author Omar ElAzazy
	 */
	private boolean showData(String response) {
		if (!populateData(response)) {
			return false;
		}
		FragmentTransaction fragmentTransaction = getActivity()
				.getSupportFragmentManager().beginTransaction();

		for (int i = 0; i < getNumberOfMembers(); i++) {
			MemberEntryFragment memberEntryFragment = new MemberEntryFragment();
			memberFragments.add(memberEntryFragment);
			Bundle bundle = new Bundle();
			bundle.putInt(Config.USER_ID, getMemberIds()[i]);
			bundle.putInt(Config.TANGLE_ID, getTangleId());
			bundle.putString(Config.MEMBER_NAME, getMemberNames()[i]);
			bundle.putInt(Config.MEMBER_BALANCE, getMemberBalances()[i]);
			bundle.putString(Config.MEMBER_AVATAR_URL, getMemberAvatarURLs()[i]);
			memberEntryFragment.setArguments(bundle);
			fragmentTransaction.add(R.id.view_member_list, memberEntryFragment);
		}

		fragmentTransaction.commit();
		return true;
	}

	/*
	 * It takes the response and populates the variables of this fragment
	 * 
	 * @return a boolean indicating if this was successful or not
	 * 
	 * @author Omar ElAzazy
	 */
	private boolean populateData(String response) {

		try {
			JSONObject json = new JSONObject(response);
			JSONArray members = json.getJSONArray("users");

			setNumberOfMembers(members.length());

			memberIds = new int[numberOfMembers];
			memberBalances = new int[numberOfMembers];
			memberNames = new String[numberOfMembers];
			memberAvatarURLs = new String[numberOfMembers];

			for (int i = 0; i < numberOfMembers; i++) {
				JSONObject member = members.getJSONObject(i);

				memberIds[i] = member.getInt("id");
				memberNames[i] = member.getString("username");
				memberBalances[i] = member.getInt("balance");
				memberAvatarURLs[i] = member.getString("iconUrl");
			}
		} catch (JSONException e) {
			return false;
		}

		return true;
	}

	/*
	 * It shows the given message in a toaster
	 * 
	 * @param String message, the message to be showed
	 * 
	 * @author Omar ElAzazy
	 */
	private void toasterShow(String message) {
		Toast.makeText(getActivity().getBaseContext(), message,
				Toast.LENGTH_LONG).show();
	}

	/*
	 * Searches for member.
	 * @param String searchString, the name being searched for
	 * @author sak93
	 */
	private void searchMembers(String searchString) {
		int memberCount = 0;
		for (int i = 0; i < getNumberOfMembers(); i++) {
			if (!memberFragments.get(i).getMemberName().toLowerCase()
					.contains(searchString.toLowerCase())) {
				memberFragments.get(i).getView().setVisibility(View.GONE);
			} else {
				memberFragments.get(i).getView().setVisibility(View.VISIBLE);
				memberCount++;
			}
		}
		if (memberCount == 0) {
			noMembers.setVisibility(View.VISIBLE);
		} else {
			noMembers.setVisibility(View.GONE);
		}
	}
	
	public void onPause(){
		super.onPause();
		isDestroyed = true;
	}
}

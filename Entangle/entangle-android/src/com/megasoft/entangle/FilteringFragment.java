package com.megasoft.entangle;

import java.util.HashMap;

import com.megasoft.config.Config;
import com.megasoft.entangle.megafragments.TangleFragment;

import android.annotation.SuppressLint;
import android.app.DialogFragment;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.AutoCompleteTextView;
import android.widget.Button;
import android.widget.EditText;

/**
 * It is a fragment that represents a pop-up window to get the filtering options
 * values. It is called when the user requests to filter the stream.
 * 
 * @author HebaAamer
 * 
 */
@SuppressLint("NewApi")
public class FilteringFragment extends DialogFragment {

	/**
	 * The domain to which the requests are sent
	 */
	private String rootResource = Config.API_BASE_URL;

	/**
	 * The HashMap that contains the mapping of the user to its id
	 */
	private HashMap<String, Integer> userToId = new HashMap<String, Integer>();

	/**
	 * The HashMap that contains the mapping of the tag to its id
	 */
	private HashMap<String, Integer> tagToId = new HashMap<String, Integer>();

	/**
	 * The view where the user writes a tag to filter with
	 */
	private AutoCompleteTextView tagText;

	/**
	 * The view where the user writes a user to filter with
	 */
	private AutoCompleteTextView userText;

	/**
	 * The view where the user writes a full text search to filter with
	 */
	private EditText fullText;
	
	private TangleFragment parent;

	/**
	 * This is method is used to create an instance of the FilteringFragment
	 * 
	 * @param tagToId
	 *            , is the hashMap that maps a tag to its id
	 * @param userToId
	 *            , is the hashMap that maps a user to its id
	 * @return an instance of the FilteringFragment class
	 */
	public static FilteringFragment createInstance(
			HashMap<String, Integer> tagToId, HashMap<String, Integer> userToId, TangleFragment parent) {
		FilteringFragment fragment = new FilteringFragment();
		fragment.parent = parent;
		fragment.tagToId = tagToId;
		fragment.userToId = userToId;
		return fragment;
	}

	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container,
			Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		View view = inflater.inflate(R.layout.fragment_filtering, container,
				false);
		setTagSuggestions(view);
		setUserSuggestions(view);
		setFullText(view);
		setButtonsActions(view);
		getDialog().setTitle("Filtering Options");
		return view;
	}

	/**
	 * This method sets the EditText used in full text search
	 * 
	 * @param view
	 *            , is the view of the fragment
	 */
	private void setFullText(View view) {
		fullText = (EditText) view.findViewById(R.id.fullTextValue);
	}

	/**
	 * This method sets the AutoCompleteTextView of the tag,and its sets its
	 * suggestions from the activity
	 * 
	 * @param view
	 *            , is the view of the fragment
	 */
	private void setTagSuggestions(View view) {
		tagText = (AutoCompleteTextView) view.findViewById(R.id.tagValue);
		tagText.setAdapter(new ArrayAdapter<String>(getActivity(),
				android.R.layout.simple_list_item_1,
				parent.getTagsSuggestions()));
	}

	/**
	 * This method sets the AutoCompleteTextView of the user,and its sets its
	 * suggestions from the activity
	 * 
	 * @param view
	 *            , is the view of the fragment
	 */
	private void setUserSuggestions(View view) {
		userText = (AutoCompleteTextView) view.findViewById(R.id.userValue);
		userText.setAdapter(new ArrayAdapter<String>(getActivity(),
				android.R.layout.simple_list_item_1,
				parent.getUsersSuggestions()));
	}

	/**
	 * This method sets the actions of the buttons in the fragment,where the
	 * filter button sends a request to filter the stream with the specified
	 * options,while the cancel button return back to the stream.
	 * 
	 * @param view
	 *            , is the view of the fragment
	 */
	private void setButtonsActions(View view) {
		Button filter = (Button) view.findViewById(R.id.doFilteration);
		filter.setOnClickListener(new View.OnClickListener() {

			@Override
			public void onClick(View v) {
				String url = "";
				String tag = tagText.getText().toString();
				String user = userText.getText().toString();
				String text = fullText.getText().toString();
				boolean putAnd = false;
				boolean putQuestionMark = false;
				if (userToId != null && userToId.containsKey(user)) {
					putQuestionMark = true;
					putAnd = true;
					url += "userid=" + userToId.get(user);
				}
				if (tag != "" && tagToId != null && tagToId.containsKey(tag)) {
					putQuestionMark = true;
					if (putAnd) {
						url += "&";
					}
					putAnd = true;
					url += "tagid=" + tagToId.get(tag);
				}
				if (text != "") {
					putQuestionMark = true;
					if (putAnd) {
						url += "&";
					}
					url += "&fulltext=" + text;
				}
				if (putQuestionMark) {
					url = "?" + url;
				}
				url = rootResource + "tangle/"
						+ parent.getTangleId()
						+ "/request" + url;
				parent.sendFilteredRequest(url
						.replace(" ", "+"));
				getDialog().dismiss();
			}
		});
		Button cancel = (Button) view.findViewById(R.id.cancelFilteration);
		cancel.setOnClickListener(new View.OnClickListener() {

			@Override
			public void onClick(View v) {
				getDialog().dismiss();
			}
		});
	}
}
